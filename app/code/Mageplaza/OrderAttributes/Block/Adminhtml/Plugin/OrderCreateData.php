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

namespace Mageplaza\OrderAttributes\Block\Adminhtml\Plugin;

use Closure;
use Magento\Sales\Block\Adminhtml\Order\Create\Data;

/**
 * Class OrderCreateData
 * @package Mageplaza\OrderAttributes\Block\Adminhtml\Plugin
 */
class OrderCreateData
{
    /**
     * @param Data $subject
     * @param Closure $proceed
     * @param string $alias
     *
     * @return string
     */
    public function aroundGetChildHtml(
        Data $subject,
        Closure $proceed,
        $alias = ''
    ) {
        $result = $proceed($alias);

        if ($alias == 'gift_options') {
            $result .= $subject->getChildHtml('mporderattributes_sales_order_create_summary');
        }

        return $result;
    }
}
