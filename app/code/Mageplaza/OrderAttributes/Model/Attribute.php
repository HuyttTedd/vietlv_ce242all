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
 * @package     Mageplaza_OrderAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\OrderAttributes\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Mageplaza\OrderAttributes\Api\Data\AttributesInterface;

/**
 * Class Attribute
 * @package Mageplaza\OrderAttributes\Model
 */
class Attribute extends AbstractModel implements AttributesInterface
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageplaza_order_attribute';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_order_attribute';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_order_attribute';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Attribute::class);
    }

    /**
     * Load attribute data by code
     *
     * @param string $code
     *
     * @return $this
     * @throws LocalizedException
     */
    public function loadByCode($code)
    {
        $this->_getResource()->loadByCode($this, $code);

        return $this;
    }

    /**
     * @param $column
     *
     * @return mixed
     * @throws LocalizedException
     */
    public function isColumnExists($column)
    {
        return $this->_getResource()->checkSalesOrderColumn($column);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeId()
    {
        return $this->getData(self::ATTRIBUTE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeId($value)
    {
        return $this->setData(self::ATTRIBUTE_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributeCode()
    {
        return $this->getData(self::ATTRIBUTE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setAttributeCode($value)
    {
        return $this->setData(self::ATTRIBUTE_CODE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getBackendType()
    {
        return $this->getData(self::BACKEND_TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setBackendType($value)
    {
        return $this->setData(self::BACKEND_TYPE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFrontendInput()
    {
        return $this->getData(self::FRONTEND_INPUT);
    }

    /**
     * {@inheritdoc}
     */
    public function setFrontendInput($value)
    {
        return $this->setData(self::FRONTEND_INPUT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFrontendLabel()
    {
        return $this->getData(self::FRONTEND_LABEL);
    }

    /**
     * {@inheritdoc}
     */
    public function setFrontendLabel($value)
    {
        return $this->setData(self::FRONTEND_LABEL, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsRequired()
    {
        return $this->getData(self::IS_REQUIRED);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsRequired($value)
    {
        return $this->setData(self::IS_REQUIRED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultValue()
    {
        return $this->getData(self::DEFAULT_VALUE);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultValue($value)
    {
        return $this->setData(self::DEFAULT_VALUE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getInputFilter()
    {
        return $this->getData(self::INPUT_FILTER);
    }

    /**
     * {@inheritdoc}
     */
    public function setInputFilter($value)
    {
        return $this->setData(self::INPUT_FILTER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFrontendClass()
    {
        return $this->getData(self::FRONTEND_CLASS);
    }

    /**
     * {@inheritdoc}
     */
    public function setFrontendClass($value)
    {
        return $this->setData(self::FRONTEND_CLASS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSortOrder()
    {
        return $this->getData(self::SORT_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setSortOrder($value)
    {
        return $this->setData(self::SORT_ORDER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getIsUsedInGrid()
    {
        return $this->getData(self::IS_USED_IN_GRID);
    }

    /**
     * {@inheritdoc}
     */
    public function setIsUsedInGrid($value)
    {
        return $this->setData(self::IS_USED_IN_GRID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getShowInFrontendOrder()
    {
        return $this->getData(self::SHOW_IN_FRONTEND_ORDER);
    }

    /**
     * {@inheritdoc}
     */
    public function setShowInFrontendOrder($value)
    {
        return $this->setData(self::SHOW_IN_FRONTEND_ORDER, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowExtensions()
    {
        return $this->getData(self::ALLOW_EXTENSIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setAllowExtensions($value)
    {
        return $this->setData(self::ALLOW_EXTENSIONS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxFileSize()
    {
        return $this->getData(self::MAX_FILE_SIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxFileSize($value)
    {
        return $this->setData(self::MAX_FILE_SIZE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldDepend()
    {
        return $this->getData(self::FIELD_DEPEND);
    }

    /**
     * {@inheritdoc}
     */
    public function setFieldDepend($value)
    {
        return $this->setData(self::FIELD_DEPEND, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getValueDepend()
    {
        return $this->getData(self::VALUE_DEPEND);
    }

    /**
     * {@inheritdoc}
     */
    public function setValueDepend($value)
    {
        return $this->setData(self::VALUE_DEPEND, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingDepend()
    {
        return $this->getData(self::SHIPPING_DEPEND);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingDepend($value)
    {
        return $this->setData(self::SHIPPING_DEPEND, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setStoreId($value)
    {
        return $this->setData(self::STORE_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerGroup()
    {
        return $this->getData(self::CUSTOMER_GROUP);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerGroup($value)
    {
        return $this->setData(self::CUSTOMER_GROUP, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return $this->getData(self::POSITION);
    }

    /**
     * {@inheritdoc}
     */
    public function setPosition($value)
    {
        return $this->setData(self::POSITION, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUseTooltip()
    {
        return $this->getData(self::USE_TOOLTIP);
    }

    /**
     * {@inheritdoc}
     */
    public function setUseTooltip($value)
    {
        return $this->setData(self::USE_TOOLTIP, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdditionalData()
    {
        return $this->getData(self::ADDITIONAL_DATA);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdditionalData($value)
    {
        return $this->setData(self::ADDITIONAL_DATA, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getLabels()
    {
        return $this->getData(self::LABELS);
    }

    /**
     * {@inheritdoc}
     */
    public function setLabels($value)
    {
        return $this->setData(self::LABELS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getTooltips()
    {
        return $this->getData(self::TOOLTIPS);
    }

    /**
     * {@inheritdoc}
     */
    public function setTooltips($value)
    {
        return $this->setData(self::TOOLTIPS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->getData(self::OPTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions($value)
    {
        return $this->setData(self::OPTIONS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($value)
    {
        return $this->setData(self::CREATED_AT, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($value)
    {
        return $this->setData(self::UPDATED_AT, $value);
    }
}
