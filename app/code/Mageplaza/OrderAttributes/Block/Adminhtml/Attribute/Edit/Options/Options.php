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

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Mageplaza\OrderAttributes\Helper\Data;
use Mageplaza\OrderAttributes\Model\Attribute;

/**
 * Class Options
 * @package Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Options
 */
class Options extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_OrderAttributes::attribute/options.phtml';

    /**
     * @var Registry
     */
    protected $_registry;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * Options constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Data $helperData,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperData = $helperData;
        $this->_registry = $registry;
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
        $inputType = strpos($attributeObject->getFrontendInput(), 'multiselect') === false ? 'radio' : 'checkbox';
        if (!empty($options['option'])) {
            $values = $this->_prepareOptionValues($options, $inputType);
        }

        return $values;
    }

    /**
     * @param array $options
     * @param string $inputType
     *
     * @return array
     */
    protected function _prepareOptionValues($options, $inputType)
    {
        $defaultValues = isset($options['default']) ? $options['default'] : [];
        $order = $options['option']['order'];
        $values = [];
        foreach ($options['option']['value'] as $id => $option) {
            $bunch = $this->_prepareAttributeOptionValues(
                $id,
                $option,
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
     * Prepare option values of attribute
     *
     * @param $id
     * @param array $option
     * @param string $inputType
     * @param array $defaultValues
     * @param $sortOrder
     *
     * @return array
     */
    protected function _prepareAttributeOptionValues($id, $option, $inputType, $defaultValues, $sortOrder)
    {
        $optionId = $id;

        $value['checked'] = in_array($optionId, $defaultValues) ? 'checked="checked"' : '';
        $value['intype'] = $inputType;
        $value['id'] = $optionId;
        $value['sort_order'] = $sortOrder;

        foreach ($this->getStores() as $store) {
            $storeId = $store->getId();
            if (isset($option[$storeId])) {
                $value['store' . $storeId] = $option[$storeId];
            }
        }

        return [$value];
    }

    /**
     * Returns stores sorted by Sort Order
     *
     * @return array
     */
    public function getStoresSortedBySortOrder()
    {
        $stores = $this->getStores();
        if (is_array($stores)) {
            usort($stores, function (
                $storeA,
                $storeB
            ) {
                if ($storeA->getSortOrder() == $storeB->getSortOrder()) {
                    return $storeA->getId() < $storeB->getId() ? -1 : 1;
                }

                return ($storeA->getSortOrder() < $storeB->getSortOrder()) ? -1 : 1;
            });
        }

        return $stores;
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
