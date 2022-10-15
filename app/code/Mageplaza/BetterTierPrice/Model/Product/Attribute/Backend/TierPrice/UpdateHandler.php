<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_BetterTierPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterTierPrice\Model\Product\Attribute\Backend\TierPrice;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductAttributeRepositoryInterface;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Framework\DataObject;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Process tier price data for handled existing product
 */
class UpdateHandler implements ExtensionInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ProductAttributeRepositoryInterface
     */
    private $attributeRepository;

    /**
     * @var GroupManagementInterface
     */
    private $groupManagement;

    /**
     * @var MetadataPool
     */
    private $metadataPoll;

    /**
     * @var Tierprice
     */
    private $tierPriceResource;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * UpdateHandler constructor.
     *
     * @param StoreManagerInterface $storeManager
     * @param ProductAttributeRepositoryInterface $attributeRepository
     * @param GroupManagementInterface $groupManagement
     * @param MetadataPool $metadataPool
     * @param Tierprice $tierPriceResource
     * @param LoggerInterface $logger
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        ProductAttributeRepositoryInterface $attributeRepository,
        GroupManagementInterface $groupManagement,
        MetadataPool $metadataPool,
        Tierprice $tierPriceResource,
        LoggerInterface $logger
    ) {
        $this->storeManager        = $storeManager;
        $this->attributeRepository = $attributeRepository;
        $this->groupManagement     = $groupManagement;
        $this->metadataPoll        = $metadataPool;
        $this->tierPriceResource   = $tierPriceResource;
        $this->logger              = $logger;
    }

    /**
     * @param object $entity
     * @param array $arguments
     *
     * @return bool|object
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute($entity, $arguments = [])
    {
        $attribute = $this->attributeRepository->get('tier_price');
        $priceRows = $entity->getData($attribute->getName());
        if ($priceRows !== null) {
            if (!is_array($priceRows)) {
                throw new InputException(
                    __('Tier prices data should be array, but actually other type is received')
                );
            }
            $websiteId       = $this->storeManager->getStore($entity->getStoreId())->getWebsiteId();
            $isGlobal        = $attribute->isScopeGlobal() || $websiteId === 0;
            $identifierField = $this->metadataPoll->getMetadata(ProductInterface::class)->getLinkField();
            $productId       = $entity->getData($identifierField);

            // prepare original data to compare
            $origPrices = $entity->getOrigData($attribute->getName());
            $old        = $this->prepareOriginalDataToCompare($origPrices, $isGlobal);
            // prepare data for save
            $new = $this->prepareNewDataForSave($priceRows, $isGlobal);

            $delete = array_diff_key($old, $new);
            $insert = array_diff_key($new, $old);
            $update = array_intersect_key($new, $old);

            $isAttributeChanged = $this->deleteValues($productId, $delete);
            $isAttributeChanged |= $this->insertValues($productId, $insert);
            $isAttributeChanged |= $this->updateValues($update, $old);

            if ($isAttributeChanged) {
                $valueChangedKey = $attribute->getName() . '_changed';
                $entity->setData($valueChangedKey, 1);
            }
        }

        return $entity;
    }

    /**
     * Get additional tier price fields
     *
     * @param array $objectArray
     *
     * @return array
     * @throws LocalizedException
     */
    private function getAdditionalFields(array $objectArray): array
    {
        try {
            $percentageValue = $this->getPercentage($objectArray);
            $discountFixed   = $this->getDiscountFixed($objectArray);

            return [
                'value'             => ($percentageValue || $discountFixed) ? null : $objectArray['price'],
                'percentage_value'  => $percentageValue ?: null,
                'mp_discount_fixed' => $discountFixed ?: null,
            ];
        } catch (Exception $e) {
            $this->logger->critical($e);
            throw new LocalizedException(__('Something went wrong while saving tier price'));
        }
    }

    /**
     * Check whether price has percentage value.
     *
     * @param array $priceRow
     *
     * @return integer|null
     */
    private function getPercentage(array $priceRow)
    {
        return isset($priceRow['percentage_value']) && is_numeric($priceRow['percentage_value'])
            ? (float) $priceRow['percentage_value']
            : null;
    }

    /**
     * Check whether price has discount fixed value.
     *
     * @param array $priceRow
     *
     * @return null
     */
    private function getDiscountFixed($priceRow)
    {
        return isset($priceRow['mp_discount_fixed']) && is_numeric($priceRow['mp_discount_fixed'])
            ? $priceRow['mp_discount_fixed']
            : null;
    }

    /**
     * Update existing tier prices for processed product
     *
     * @param array $valuesToUpdate
     * @param array $oldValues
     *
     * @return boolean
     */
    private function updateValues(array $valuesToUpdate, array $oldValues): bool
    {
        $isChanged = false;
        foreach ($valuesToUpdate as $key => $value) {
            if ((!empty($value['value']) && (float) $oldValues[$key]['price'] !== (float) $value['value'])
                || $this->getPercentage($oldValues[$key]) !== $this->getPercentage($value)
                || $this->getDiscountFixed($oldValues[$key]) !== $this->getDiscountFixed($value)
            ) {
                $price = new DataObject(
                    [
                        'value_id'          => $oldValues[$key]['price_id'],
                        'value'             => $value['value'],
                        'percentage_value'  => $this->getPercentage($value),
                        'mp_discount_fixed' => $this->getDiscountFixed($value)
                    ]
                );
                $this->tierPriceResource->savePriceData($price);
                $isChanged = true;
            }
        }

        return $isChanged;
    }

    /**
     * Insert new tier prices for processed product
     *
     * @param int $productId
     * @param array $valuesToInsert
     *
     * @return bool
     * @throws Exception
     */
    private function insertValues(int $productId, array $valuesToInsert): bool
    {
        $isChanged       = false;
        $identifierField = $this->metadataPoll->getMetadata(ProductInterface::class)->getLinkField();
        foreach ($valuesToInsert as $data) {
            $price = new DataObject($data);
            $price->setData(
                $identifierField,
                $productId
            );
            $this->tierPriceResource->savePriceData($price);
            $isChanged = true;
        }

        return $isChanged;
    }

    /**
     * Delete tier price values for processed product
     *
     * @param int $productId
     * @param array $valuesToDelete
     *
     * @return bool
     */
    private function deleteValues(int $productId, array $valuesToDelete): bool
    {
        $isChanged = false;
        foreach ($valuesToDelete as $data) {
            $this->tierPriceResource->deletePriceData($productId, null, $data['price_id']);
            $isChanged = true;
        }

        return $isChanged;
    }

    /**
     * Get generated price key based on price data
     *
     * @param array $priceData
     *
     * @return string
     */
    private function getPriceKey(array $priceData): string
    {
        $key = implode(
            '-',
            array_merge([$priceData['website_id'], $priceData['cust_group']], [(int) $priceData['price_qty']])
        );

        return $key;
    }

    /**
     * Prepare tier price data by provided price row data
     *
     * @param array $data
     *
     * @return array
     * @throws LocalizedException
     */
    private function prepareTierPrice(array $data): array
    {
        $useForAllGroups = (int) $data['cust_group'] === $this->groupManagement->getAllCustomersGroup()->getId();
        $customerGroupId = $useForAllGroups ? 0 : $data['cust_group'];
        $tierPrice       = array_merge(
            $this->getAdditionalFields($data),
            [
                'website_id'        => $data['website_id'],
                'all_groups'        => (int) $useForAllGroups,
                'customer_group_id' => $customerGroupId,
                'value'             => $data['price'] ?? null,
                'qty'               => (int) $data['price_qty']
            ]
        );

        return $tierPrice;
    }

    /**
     * Check by id is website global
     *
     * @param int $websiteId
     *
     * @return bool
     */
    private function isWebsiteGlobal(int $websiteId): bool
    {
        return $websiteId === 0;
    }

    /**
     * @param array|null $origPrices
     * @param bool $isGlobal
     *
     * @return array
     */
    private function prepareOriginalDataToCompare($origPrices, $isGlobal = true): array
    {
        $old = [];
        if (is_array($origPrices)) {
            foreach ($origPrices as $data) {
                if ($isGlobal === $this->isWebsiteGlobal((int) $data['website_id'])) {
                    $key       = $this->getPriceKey($data);
                    $old[$key] = $data;
                }
            }
        }

        return $old;
    }

    /**
     * @param array $priceRows
     * @param bool $isGlobal
     *
     * @return array
     * @throws LocalizedException
     */
    private function prepareNewDataForSave($priceRows, $isGlobal = true): array
    {
        $new       = [];
        $priceRows = array_filter($priceRows);
        foreach ($priceRows as $data) {
            if (empty($data['delete'])
                && (!empty($data['price_qty'])
                    || isset($data['cust_group'])
                    || $isGlobal === $this->isWebsiteGlobal((int) $data['website_id']))
            ) {
                $key       = $this->getPriceKey($data);
                $new[$key] = $this->prepareTierPrice($data);
            }
        }

        return $new;
    }
}
