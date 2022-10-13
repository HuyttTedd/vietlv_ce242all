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

namespace Mageplaza\BetterChangeQty\Plugin\Block\Product;

use Closure;
use Mageplaza\BetterChangeQty\Helper\Data;

/**
 * Class AbstractProduct
 *
 * @package Mageplaza\BetterChangeQty\Plugin\Block\Product
 */
class ListProduct
{
    /**
     * @var Data
     */
    protected $data;

    /**
     * ListProduct constructor.
     *
     * @param Data $data
     */
    public function __construct(Data $data)
    {
        $this->data = $data;
    }

    /**
     * @param \Magento\Catalog\Block\Product\ListProduct $subject
     * @param Closure $proceed
     * @param $name
     *
     * @return string
     * @SuppressWarnings(Unused)
     */
    public function aroundGetBlockHtml(\Magento\Catalog\Block\Product\ListProduct $subject, Closure $proceed, $name)
    {
        $html = $proceed($name);

        if (!$this->data->isEnabled()) {
            return $html;
        }

        if ($name == 'formkey') {
            $html .= '<input type="hidden" name="qty" value="1"/>';
        }

        return $html;
    }
}
