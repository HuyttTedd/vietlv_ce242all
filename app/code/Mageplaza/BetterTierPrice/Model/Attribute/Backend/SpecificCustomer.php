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

namespace Mageplaza\BetterTierPrice\Model\Attribute\Backend;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Action;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\BetterTierPrice\Helper\Data;
use Mageplaza\BetterTierPrice\Model\Config\Source\ProductPriceOptions;
use Psr\Log\LoggerInterface;

/**
 * Class SpecificCustomer
 * @package Mageplaza\BetterTierPrice\Model\Attribute\Backend
 */
class SpecificCustomer extends AbstractBackend
{
    /**
     * @var ProductResource
     */
    private $productResource;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Action
     */
    private $action;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * SpecificCustomer constructor.
     *
     * @param ProductResource $productResource
     * @param StoreManagerInterface $storeManager
     * @param Action $action
     * @param LoggerInterface $logger
     * @param Data $helperData
     */
    public function __construct(
        ProductResource $productResource,
        StoreManagerInterface $storeManager,
        Action $action,
        LoggerInterface $logger,
        Data $helperData
    ) {
        $this->productResource = $productResource;
        $this->storeManager    = $storeManager;
        $this->action          = $action;
        $this->logger          = $logger;
        $this->helperData      = $helperData;
    }

    /**
     * @param Product $object
     *
     * @return int
     * @throws NoSuchEntityException
     */
    private function getWebsiteId($object)
    {
        if ($this->storeManager->hasSingleStore() || count($object->getWebsiteIds()) < 2) {
            return 0;
        }

        return $this->storeManager->getStore($object->getStoreId())->getWebsiteId();
    }

    /**
     * @param Product $object
     *
     * @return $this|AbstractBackend
     */
    public function beforeSave($object)
    {
        if (!$this->helperData->isEnabled($object->getStoreId())) {
            return $this;
        }
        $attributeCode    = $this->getAttribute()->getName();
        $specificCustomer = $object->getData($attributeCode);
        $data             = $specificCustomer;

        if (!is_string($data)) {
            $data = Data::jsonEncode($data);
        }
        $object->setData($attributeCode, $data);

        return $this;
    }

    /**
     * @param Product $object
     *
     * @return $this|AbstractBackend
     * @throws NoSuchEntityException
     */
    public function afterSave($object)
    {
        if (!$this->helperData->isEnabled($object->getStoreId())) {
            return $this;
        }
        $attributeCode    = $this->getAttribute()->getName();
        $specificCustomer = $object->getData($attributeCode);
        $specificCustomer = json_decode($specificCustomer, true);

        if ($specificCustomer && is_array($specificCustomer)) {
            foreach ($specificCustomer as &$item) {
                if (isset($item['price']) && !$item['price']
                    && $item['value_type'] == ProductPriceOptions::VALUE_FIXED) {
                    $item['price'] = 0;
                }
            }

            $specificCustomer = json_encode($specificCustomer, true);

            $this->action->updateAttributes(
                [$object->getEntityId()],
                ['mp_specific_customer' => $specificCustomer],
                $this->getWebsiteId($object)
            );
        }

        return $this;
    }

    /**
     * @param Product $object
     *
     * @return $this|AbstractBackend
     * @throws NoSuchEntityException
     */
    public function afterLoad($object)
    {
        $attributeCode = $this->getAttribute()->getName();
        $dataConfig    = $this->productResource
            ->getAttributeRawValue($object->getEntityId(), $attributeCode, $this->getWebsiteId($object));
        $data          = $dataConfig ?: '';
        if (!$this->helperData->isEnabled($object->getStoreId())) {
            return $this;
        }
        if (is_string($data)) {
            try {
                $data = Data::jsonDecode($data);
                $object->setData($attributeCode, $data);
                $object->setOrigData($attributeCode, $data);
            } catch (Exception $e) {
                $this->logger->critical($e);
                $object->setData($attributeCode, []);
            }
        }

        return $this;
    }
}
