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

namespace Mageplaza\OrderAttributes\Api\Data;

/**
 * Interface AttributesInterface
 * @package Mageplaza\OrderAttributes\Api\Data
 */
interface AttributesInterface
{
    const ATTRIBUTE_ID           = 'attribute_id';
    const ATTRIBUTE_CODE         = 'attribute_code';
    const BACKEND_TYPE           = 'backend_type';
    const FRONTEND_INPUT         = 'frontend_input';
    const FRONTEND_LABEL         = 'frontend_label';
    const IS_REQUIRED            = 'is_required';
    const DEFAULT_VALUE          = 'default_value';
    const INPUT_FILTER           = 'input_filter';
    const FRONTEND_CLASS         = 'frontend_class';
    const SORT_ORDER             = 'sort_order';
    const IS_USED_IN_GRID        = 'is_used_in_grid';
    const SHOW_IN_FRONTEND_ORDER = 'show_in_frontend_order';
    const ALLOW_EXTENSIONS       = 'allow_extensions';
    const MAX_FILE_SIZE          = 'max_file_size';
    const FIELD_DEPEND           = 'field_depend';
    const VALUE_DEPEND           = 'value_depend';
    const SHIPPING_DEPEND        = 'shipping_depend';
    const STORE_ID               = 'store_id';
    const CUSTOMER_GROUP         = 'customer_group';
    const POSITION               = 'position';
    const USE_TOOLTIP            = 'use_tooltip';
    const ADDITIONAL_DATA        = 'additional_data';
    const LABELS                 = 'labels';
    const TOOLTIPS               = 'tooltips';
    const OPTIONS                = 'options';
    const CREATED_AT             = 'created_at';
    const UPDATED_AT             = 'updated_at';

    /**
     * @return int
     */
    public function getAttributeId();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setAttributeId($value);

    /**
     * @return string
     */
    public function getAttributeCode();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAttributeCode($value);

    /**
     * @return string
     */
    public function getBackendType();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setBackendType($value);

    /**
     * @return string
     */
    public function getFrontendInput();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFrontendInput($value);

    /**
     * @return string
     */
    public function getFrontendLabel();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFrontendLabel($value);

    /**
     * @return int
     */
    public function getIsRequired();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setIsRequired($value);

    /**
     * @return string
     */
    public function getDefaultValue();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setDefaultValue($value);

    /**
     * @return string
     */
    public function getInputFilter();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setInputFilter($value);

    /**
     * @return string
     */
    public function getFrontendClass();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setFrontendClass($value);

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setSortOrder($value);

    /**
     * @return int
     */
    public function getIsUsedInGrid();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setIsUsedInGrid($value);

    /**
     * @return int
     */
    public function getShowInFrontendOrder();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setShowInFrontendOrder($value);

    /**
     * @return string
     */
    public function getAllowExtensions();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAllowExtensions($value);

    /**
     * @return int
     */
    public function getMaxFileSize();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setMaxFileSize($value);

    /**
     * @return int
     */
    public function getFieldDepend();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setFieldDepend($value);

    /**
     * @return string
     */
    public function getValueDepend();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValueDepend($value);

    /**
     * @return string
     */
    public function getShippingDepend();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setShippingDepend($value);

    /**
     * @return string
     */
    public function getStoreId();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setStoreId($value);

    /**
     * @return string
     */
    public function getCustomerGroup();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCustomerGroup($value);

    /**
     * @return string
     */
    public function getPosition();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPosition($value);

    /**
     * @return int
     */
    public function getUseTooltip();

    /**
     * @param int $value
     *
     * @return $this
     */
    public function setUseTooltip($value);

    /**
     * @return string
     */
    public function getAdditionalData();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setAdditionalData($value);

    /**
     * @return string
     */
    public function getLabels();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setLabels($value);

    /**
     * @return string
     */
    public function getTooltips();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setTooltips($value);

    /**
     * @return string
     */
    public function getOptions();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setOptions($value);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setCreatedAt($value);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setUpdatedAt($value);
}
