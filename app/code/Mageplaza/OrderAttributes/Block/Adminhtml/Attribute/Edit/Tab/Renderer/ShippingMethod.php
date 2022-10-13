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

namespace Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Tab\Renderer;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;
use Magento\Framework\Registry;
use Mageplaza\OrderAttributes\Helper\Data;

/**
 * Class ShippingMethod
 * @package Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Tab\Renderer
 */
class ShippingMethod extends AbstractElement
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * ShippingMethod constructor.
     *
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param Registry $coreRegistry
     * @param Data $helperData
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        Registry $coreRegistry,
        Data $helperData,
        $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->helperData = $helperData;

        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);

        $this->setType('multiselect');
    }

    /**
     * Get the Html for the element.
     *
     * @return string
     */
    public function getElementHtml()
    {
        $html = '';
        $carriers = $this->helperData->getShippingMethods();
        $attrObj = $this->_coreRegistry->registry('entity_attribute');
        $shippingDepend = explode(',', $attrObj->getShippingDepend());
        $html .= '<select name="shipping_depend[]" id="shipping_depend" size="10" multiple="multiple"  class=" select multiselect admin__control-multiselect">';
        foreach ($carriers as $carrier) {
            $html .= '<optgroup label="' . $carrier['label'] . '">';
            foreach ($carrier['value'] as $child) {
                $html .= '<option value="' . $child['value'] . '"';
                $html .= (in_array($child['value'], $shippingDepend)) ? ' selected>' : '>';
                $html .= $child['label'];
                $html .= '</option>';
            }
            $html .= '</optgroup>';
        }
        $html .= '</select>';
        $html .= '<div id="mp-select-all-container"><input id="mp-select-all" type="checkbox" value="select_all_methods" />';
        $html .= '<label for="mp-select-all">' . __('Select All') . '</label></div>';

        return $html;
    }
}
