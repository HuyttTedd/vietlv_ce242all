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

use Magento\Sales\Block\Adminhtml\Order\View\Tab\Info;

/**
 * Class OrderViewTabInfo
 * @package Mageplaza\OrderAttributes\Block\Adminhtml\Plugin
 */
class OrderViewTabInfo
{
    /**
     * @param Info $subject
     * @param $result
     *
     * @return string
     */
    public function afterGetGiftOptionsHtml(Info $subject, $result)
    {
        $result .= $subject->getChildHtml('mporderattributes_sales_order_view');

        return $result;
    }
}
