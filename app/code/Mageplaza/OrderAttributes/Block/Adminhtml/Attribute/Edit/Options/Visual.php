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

namespace Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Options;

use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\Product\Media\Config;
use Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\CollectionFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\Validator\UniversalFactory;
use Magento\Swatches\Helper\Media;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;

/**
 * Class Visual
 * @package Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Options
 */
class Visual extends \Magento\Swatches\Block\Adminhtml\Attribute\Edit\Options\Visual
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_OrderAttributes::attribute/visual.phtml';

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Visual constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param CollectionFactory $attrOptionCollectionFactory
     * @param UniversalFactory $universalFactory
     * @param Config $mediaConfig
     * @param Media $swatchHelper
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        CollectionFactory $attrOptionCollectionFactory,
        UniversalFactory $universalFactory,
        Config $mediaConfig,
        Media $swatchHelper,
        Data $helperData,
        array $data = []
    ) {
        $this->helperData = $helperData;
        parent::__construct(
            $context,
            $registry,
            $attrOptionCollectionFactory,
            $universalFactory,
            $mediaConfig,
            $swatchHelper,
            $data
        );
    }

    /**
     * Retrieve stores collection with default store
     *
     * @return array
     */
    public function getStores()
    {
        if (!$this->hasStores()) {
            $this->setData('stores', $this->_storeManager->getStores(true));
        }

        return $this->_getData('stores');
    }

    /**
     * Retrieve attribute option values if attribute input type select or multi-select
     *
     * @return array
     */
    public function getOptionValues()
    {
        $values = [];
        $attributeObject = $this->getAttributeObject();
        $options = $this->helperData->jsonDecodeData($attributeObject->getOptions());
        $swatchOptions = $this->helperData->jsonDecodeData($attributeObject->getAdditionalData());
        $inputType = strpos($attributeObject->getFrontendInput(), 'multiselect') === false ? 'radio' : 'checkbox';
        if (!empty($options['optionvisual'])) {
            $values = $this->prepareOptionValues($options, $swatchOptions, $inputType);
        }

        return $values;
    }

    /**
     * @param $options
     * @param $swatchOptions
     * @param $inputType
     *
     * @return array
     */
    protected function prepareOptionValues($options, $swatchOptions, $inputType)
    {
        $defaultValues = isset($options['defaultvisual']) ? $options['defaultvisual'] : [];
        $order = $options['optionvisual']['order'];
        $values = [];
        foreach ($options['optionvisual']['value'] as $id => $option) {
            $swatchValue = isset($swatchOptions[$id]['swatch_value']) ? $swatchOptions[$id]['swatch_value'] : null;
            $bunch = $this->_prepareAttributeOptionValues(
                $id,
                $option,
                $swatchValue,
                $inputType,
                $defaultValues,
                $order[$id]
            );
            foreach ($bunch as $value) {
                $values[] = new DataObject($value);
            }
        }

        return $values;
    }

    /**
     * Prepare option values of user defined attribute
     *
     * @param $optionId
     * @param array $option
     * @param $swatchValue
     * @param string $inputType
     * @param array $defaultValues
     * @param $sortOrder
     *
     * @return array
     */
    protected function _prepareAttributeOptionValues(
        $optionId,
        $option,
        $swatchValue,
        $inputType,
        $defaultValues,
        $sortOrder
    ) {
        $value['checked'] = in_array($optionId, $defaultValues) ? 'checked="checked"' : '';
        $value['intype'] = $inputType;
        $value['id'] = $optionId;
        $value['sort_order'] = $sortOrder;

        foreach ($this->getStores() as $store) {
            $storeId = $store->getId();
            $value['store' . $storeId] = $option[$storeId];
            $value['swatch' . $storeId] = $this->_reformatSwatchLabels($swatchValue);
            $value['defaultswatch' . $storeId] = $swatchValue;
        }

        return [$value];
    }

    /**
     * Parse swatch labels for template
     *
     * @param $swatchValue
     *
     * @return string
     */
    protected function _reformatSwatchLabels($swatchValue)
    {
        if (strpos($swatchValue, '#') === 0) {
            return 'background-color: ' . $swatchValue;
        } elseif (strpos($swatchValue, '/') === 0) {
            $mediaUrl = $this->swatchHelper->getSwatchMediaUrl();

            return 'background: url(' . $mediaUrl . $swatchValue . '); background-size: cover;';
        }

        return null;
    }

    /**
     * Retrieve attribute object from registry
     *
     * @return Attribute
     */
    protected function getAttributeObject()
    {
        return $this->_registry->registry('entity_attribute');
    }
}
