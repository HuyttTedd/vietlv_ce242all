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
 * Class ChangeQtyStep
 *
 * @package Mageplaza\BetterChangeQty\Model\Config\Source
 */
class ChangeQtyStep implements OptionSourceInterface
{
    const PRODUCT = 1;
    const FIXED   = 2;
    const CUSTOM  = 3;

    /**
     * Retrieve option array
     *
     * @return string[]
     */
    public function getOptionArray()
    {
        return [
            self::PRODUCT => __('Product Qty Increment'),
            self::FIXED   => __('Fixed Value'),
            self::CUSTOM  => __('Custom Value'),
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
