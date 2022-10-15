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

namespace Mageplaza\BetterTierPrice\Model\Product\Attribute\Backend;

use Exception;
use Magento\Catalog\Helper\Data as CatalogHelper;
use Magento\Catalog\Model\Attribute\ScopeOverriddenValue;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Backend\GroupPrice\AbstractGroupPrice;
use Magento\Catalog\Model\Product\Type;
use Magento\Catalog\Model\Product\Type\Price;
use Magento\Catalog\Model\ResourceModel\Product\Attribute\Backend\Tierprice as ProductAttributeTierprice;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Customer\Model\Group;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\DataObject;
use Magento\Framework\EntityManager\Operation\Read\ReadAttributes;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\FormatInterface;
use Magento\Framework\Phrase;
use Magento\Store\Model\StoreManagerInterface;
use Mageplaza\BetterTierPrice\Helper\Data;

/**
 * Class Tierprice
 * @package Mageplaza\BetterTierPrice\Model\Product\Attribute\Backend
 */
class Tierprice extends AbstractGroupPrice
{
    /**
     * Catalog product attribute backend tierprice
     *
     * @var ProductAttributeTierprice
     */
    protected $_productAttributeBackendTierprice;

    /**
     * @var ReadAttributes
     */
    protected $readAttributes;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @param CurrencyFactory $currencyFactory
     * @param StoreManagerInterface $storeManager
     * @param CatalogHelper $catalogData
     * @param ScopeConfigInterface $config
     * @param FormatInterface $localeFormat
     * @param Type $catalogProductType
     * @param GroupManagementInterface $groupManagement
     * @param ProductAttributeTierprice $productAttributeTierprice
     * @param ReadAttributes $readAttributes
     * @param Data $helperData
     * @param ScopeOverriddenValue|null $scopeOverriddenValue
     */
    public function __construct(
        CurrencyFactory $currencyFactory,
        StoreManagerInterface $storeManager,
        CatalogHelper $catalogData,
        ScopeConfigInterface $config,
        FormatInterface $localeFormat,
        Type $catalogProductType,
        GroupManagementInterface $groupManagement,
        ProductAttributeTierprice $productAttributeTierprice,
        ReadAttributes $readAttributes,
        Data $helperData,
        ScopeOverriddenValue $scopeOverriddenValue = null
    ) {
        $this->_productAttributeBackendTierprice = $productAttributeTierprice;
        $this->readAttributes                    = $readAttributes;
        $this->helperData                        = $helperData;

        parent::__construct(
            $currencyFactory,
            $storeManager,
            $catalogData,
            $config,
            $localeFormat,
            $catalogProductType,
            $groupManagement,
            $scopeOverriddenValue
        );
    }

    /**
     * Retrieve resource instance
     *
     * @return ProductAttributeTierprice
     */
    protected function _getResource()
    {
        return $this->_productAttributeBackendTierprice;
    }

    /**
     * @param array $objectArray
     *
     * @return array
     * @throws Exception
     */
    protected function _getAdditionalUniqueFields($objectArray)
    {
        try {
            $uniqueFields        = parent::_getAdditionalUniqueFields($objectArray);
            $uniqueFields['qty'] = $objectArray['price_qty'] * 1;

            return $uniqueFields;
        } catch (Exception $e) {
            throw new LocalizedException(__('Required fields cannot empty'));
        }
    }

    /**
     * @inheritdoc
     */
    protected function getAdditionalFields($objectArray)
    {
        $percentageValue = $this->getPercentage($objectArray);
        $discountFixed   = $this->getDiscountFixed($objectArray);

        return [
            'value'             => ($percentageValue || $discountFixed) ? null : $objectArray['price'],
            'percentage_value'  => $percentageValue ?: null,
            'mp_discount_fixed' => $discountFixed ?: null,
        ];
    }

    /**
     * Error message when duplicates
     *
     * @return Phrase
     */
    protected function _getDuplicateErrorMessage()
    {
        return __('We found a duplicate website, tier price, customer group and quantity.');
    }

    /**
     * Whether tier price value fixed or percent of original price
     *
     * @param Price $priceObject
     *
     * @return bool
     */
    protected function _isPriceFixed($priceObject)
    {
        return $priceObject->isTierPriceFixed();
    }

    /**
     * By default attribute value is considered non-scalar that can be stored in a generic way
     *
     * @return bool
     */
    public function isScalar()
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function validate($object)
    {
        $attribute = $this->getAttribute();
        $priceRows = $object->getData($attribute->getName());
        $priceRows = array_filter((array) $priceRows);

        foreach ($priceRows as $priceRow) {
            $percentage    = $this->getPercentage($priceRow);
            $discountFixed = $this->getDiscountFixed($priceRow);
            if ($discountFixed !== null && (!$this->isPositiveOrZero($discountFixed))) {
                throw new LocalizedException(
                    __('Discount Fixed value must be a number greater than 0.')
                );
            }
            if ($percentage !== null && (!$this->isPositiveOrZero($percentage) || $percentage > 100)) {
                throw new LocalizedException(
                    __('Percentage value must be a number between 0 and 100.')
                );
            }
        }

        return parent::validate($object);
    }

    /**
     * @inheritdoc
     */
    protected function validatePrice(array $priceRow)
    {
        if (!$this->getPercentage($priceRow) && !$this->getDiscountFixed($priceRow)) {
            parent::validatePrice($priceRow);
        }
    }

    /**
     * @inheritdoc
     * @throws NoSuchEntityException
     */
    protected function modifyPriceData($object, $data)
    {
        /** @var Product $object */
        $data  = parent::modifyPriceData($object, $data);
        $price = $object->getPrice();
        foreach ($data as $key => $tierPrice) {
            $percentageValue = $this->getPercentage($tierPrice);
            $discountFixed   = $this->getDiscountFixed($tierPrice);
            if ($percentageValue) {
                $data[$key]['price']         = $price * (1 - $percentageValue / 100);
                $data[$key]['website_price'] = $data[$key]['price'];
            } elseif ($discountFixed) {
                $data[$key]['price']         = ($price - $discountFixed) > 0 ? ($price - $discountFixed) : 0;
                $data[$key]['website_price'] = $data[$key]['price'];
            }
        }
        $data = $this->prepareTierPriceData($data, $object);

        return $data;
    }

    /**
     * @param array $data
     * @param Product $object
     *
     * @return mixed
     * @throws NoSuchEntityException
     */
    private function prepareTierPriceData($data, $object)
    {
        $objectManager = ObjectManager::getInstance();
        if ($this->helperData->isAdmin() || !$this->helperData->isEnabled($this->_storeManager->getStore()->getId())) {
            return $data;
        }
        $customerSession = $objectManager->create(CustomerSession::class);
        $customerId      = $customerSession ? (int) $customerSession->getCustomerId() : null;
        if (!$object->hasData('mp_specific_customer')) {
            $this->readAttributes->execute($object, ['entity_id' => $object->getEntityId()]);
        }
        $attribute = $object->getResource()->getAttribute('mp_specific_customer');
        if ($attribute) {
            $attribute->getBackend()->afterLoad($object);
        }
        $specificCustomerData = $object->getData('mp_specific_customer');
        if (!$customerId || empty($specificCustomerData)) {
            return $data;
        }
        if (is_string($specificCustomerData)) {
            $specificCustomerData = Data::jsonDecode($specificCustomerData);
        }
        if ($this->helperData->isSpecificCustomerEnabled()) {
            $data = $this->prepareDataForSpecificCustomer($data, $specificCustomerData, $object, $customerId);
        }

        return $data;
    }

    /**
     * @param array $data
     * @param array $specificCustomerData
     * @param Product $object
     * @param int $customerId
     *
     * @return array
     */
    private function prepareDataForSpecificCustomer($data, $specificCustomerData, $object, $customerId)
    {
        $specificCustomersData = $this->prepareSpecificCustomersData($specificCustomerData, $object, $customerId);

        if (!empty($specificCustomersData)) {
            foreach ($specificCustomersData as $specificCustomer) {
                $isNew = true;
                foreach ($data as &$tierPrice) {
                    if ($specificCustomer['website_id'] === $tierPrice['website_id']
                        && $specificCustomer['price_qty'] === $tierPrice['price_qty']
                    ) {
                        if ($tierPrice['price'] > $specificCustomer['price']) {
                            $tierPrice['price']             = $specificCustomer['price'];
                            $tierPrice['percentage_value']  = $specificCustomer['percentage_value'];
                            $tierPrice['mp_discount_fixed'] = $specificCustomer['mp_discount_fixed'];
                            $tierPrice['website_price']     = $specificCustomer['website_price'];
                        }
                        $isNew = false;
                        break;
                    }
                }
                unset($tierPrice);
                if ($isNew) {
                    $data[] = $specificCustomer;
                }
            }
        }
        usort($data, function ($tierA, $tierB) {
            return ($tierA['price_qty'] <= $tierB['price_qty']) ? -1 : 1;
        });

        return $data;
    }

    /**
     * @param array $specificCustomerData
     * @param Product $object
     * @param int $customerId
     *
     * @return array
     */
    private function prepareSpecificCustomersData($specificCustomerData, $object, $customerId)
    {
        $specificCustomersData = [];
        foreach ($specificCustomerData as $datum) {
            if ((int) $datum['customer_id'] === $customerId) {
                $price               = $object->getPrice();
                $datum['all_groups'] = 1;
                $datum['cust_group'] = Group::CUST_GROUP_ALL;
                switch ($datum['value_type']) {
                    case 'fixed':
                        $datum['percentage_value']  = null;
                        $datum['mp_discount_fixed'] = null;
                        break;
                    case 'percent':
                        $datum['mp_discount_fixed'] = null;
                        $datum['price']             = $price * (1 - $datum['percentage_value'] / 100);
                        break;
                    case 'mp_discount_fixed':
                        $datum['percentage_value'] = null;
                        $datum['price']            = ($price - $datum['mp_discount_fixed']) > 0
                            ? ($price - $datum['mp_discount_fixed'])
                            : 0;
                        break;
                }
                $datum['website_price'] = $datum['price'];

                $specificCustomersData[] = $datum;
            }
        }

        return $specificCustomersData;
    }

    /**
     * @param array $valuesToUpdate
     * @param array $oldValues
     *
     * @return boolean
     */
    protected function updateValues(array $valuesToUpdate, array $oldValues)
    {
        $isChanged = false;
        foreach ($valuesToUpdate as $key => $value) {
            if ((!empty($value['value']) && $oldValues[$key]['price'] != $value['value'])
                || $this->getPercentage($oldValues[$key]) != $this->getPercentage($value)
                || $this->getDiscountFixed($oldValues[$key]) != $this->getDiscountFixed($value)
            ) {
                $price = new DataObject(
                    [
                        'value_id'          => $oldValues[$key]['price_id'],
                        'value'             => $value['value'],
                        'percentage_value'  => $this->getPercentage($value),
                        'mp_discount_fixed' => $this->getDiscountFixed($value)
                    ]
                );
                $this->_getResource()->savePriceData($price);

                $isChanged = true;
            }
        }

        return $isChanged;
    }

    /**
     * Check whether price has percentage value.
     *
     * @param array $priceRow
     *
     * @return null
     */
    private function getPercentage($priceRow)
    {
        return isset($priceRow['percentage_value']) && is_numeric($priceRow['percentage_value'])
            ? $priceRow['percentage_value']
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
}
