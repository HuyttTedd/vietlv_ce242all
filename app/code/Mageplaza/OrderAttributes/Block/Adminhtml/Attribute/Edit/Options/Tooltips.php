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

/**
 * Class Tooltips
 * @package Mageplaza\OrderAttributes\Block\Adminhtml\Attribute\Edit\Options
 */
class Tooltips extends Labels
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_OrderAttributes::attribute/tooltips.phtml';

    /**
     * Retrieve tooltips of attribute for each store
     *
     * @return array
     */
    public function getTooltipValues()
    {
        $attrObj = $this->_registry->registry('entity_attribute');
        $values = [];
        $storeLabels = $attrObj->getTooltips() ? $this->helperData->jsonDecodeData($attrObj->getTooltips()) : [];
        foreach ($this->getStores() as $store) {
            $storeId = (int)$store->getId();
            if ($storeId !== 0) {
                $values[$storeId] = isset($storeLabels[$storeId]) ? $storeLabels[$storeId] : '';
            }
        }

        return $values;
    }
}
