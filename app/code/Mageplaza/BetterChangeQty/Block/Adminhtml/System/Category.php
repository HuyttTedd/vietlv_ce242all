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
 * @package     Mageplaza_BetterChangeQty
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\BetterChangeQty\Block\Adminhtml\System;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Catalog\Ui\Component\Product\Form\Categories\Options;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Mageplaza\BetterChangeQty\Helper\Data;

/**
 * Class Category
 * @package Mageplaza\BetterChangeQty\Block\Adminhtml\System
 */
class Category extends Field
{
    /**
     * @var Options
     */
    protected $_option;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var Data
     */
    private $helper;

    /**
     * Category constructor.
     *
     * @param Context $context
     * @param Options $options
     * @param CollectionFactory $collectionFactory
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Options $options,
        CollectionFactory $collectionFactory,
        Data $helper,
        array $data = []
    ) {
        $this->_option           = $options;
        $this->collectionFactory = $collectionFactory;
        $this->helper            = $helper;

        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function _getElementHtml(AbstractElement $element)
    {
        $html = '<div class="admin__field-control">';

        $html .= '<div id="mpbetterchangeqty_general_apply_category"  class="admin__field" data-bind="scope:\'category\'" data-index="index">';
        $html .= '<!-- ko foreach: elems() -->';
        $html .= '<input name="groups[general][fields][apply_category][value]" data-bind="value: value" style="display: none"/>';
        $html .= '<!-- ko template: elementTmpl --><!-- /ko -->';
        $html .= '<!-- /ko -->';
        $html .= '</div>';

        $html .= $this->getScriptHtml();

        return $html;
    }

    /**
     * Attach Slider Category suggest widget initialization
     *
     * @return string
     */
    public function getScriptHtml()
    {
        $html = '<script type="text/x-magento-init">
            {
                "*": {
                    "Magento_Ui/js/core/app": {
                        "components": {
                            "category": {
                                "component": "uiComponent",
                                "children": {
                                    "select_category": {
                                        "component": "Magento_Catalog/js/components/new-category",
                                        "config": {
                                            "filterOptions": true,
                                            "disableLabel": true,
                                            "chipsEnabled": true,
                                            "levelsVisibility": "1",
                                            "elementTmpl": "ui/grid/filters/elements/ui-select",
                                            "options": ' . Data::jsonEncode($this->_option->toOptionArray()) . ',
                                            "value": ' . Data::jsonEncode($this->getValues()) . ',
                                            "listens": {
                                                "index=create_category:responseData": "setParsed",
                                                "newOption": "toggleOptionSelected"
                                            },
                                            "config": {
                                                "dataScope": "select_category",
                                                "sortOrder": 10
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        </script>';

        return $html;
    }

    /**
     * Get values for select
     *
     * @return array
     */
    public function getValues()
    {
        $data = $this->getConfigData();
        if (empty($data['mpbetterchangeqty/general/apply_category']) && !$this->helper->getConfigGeneral('apply_category')) {
            return [];
        }

        try {
            $values = $data['mpbetterchangeqty/general/apply_category'];
        } catch (Exception $exception) {
            $values = $this->helper->getConfigGeneral('apply_category');
        }
        if (!is_array($values)) {
            $values = explode(',', $values);
        }

        if (!count($values)) {
            return [];
        }

        $options = [];
        /** @var \Magento\Catalog\Model\Category $category */
        foreach ($this->collectionFactory->create()->addIdFilter($values)->getItems() as $category) {
            $options[] = $category->getId();
        }

        return $options;
    }
}
