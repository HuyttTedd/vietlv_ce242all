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

namespace Mageplaza\BetterChangeQty\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class ApplyPage
 * @package Mageplaza\BetterChangeQty\Model\Config\Source
 */
class ApplyPage implements OptionSourceInterface
{
    const CATEGORY = 'category';
    const PRODUCT  = 'product';
    const WISHLIST = 'wishlist';

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionArray()
    {
        return [
            self::CATEGORY => __('Product List page'),
            self::PRODUCT  => __('Product View page'),
            self::WISHLIST => __('Wish List page'),
        ];
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function toOptionArray()
    {
        $result = [];

        foreach (self::getOptionArray() as $index => $value) {
            $result[] = ['value' => $index, 'label' => $value];
        }

        return $result;
    }
}
