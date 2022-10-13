<?php

namespace Mageplaza\OrderAttributes\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Position
 * @package Mageplaza\OrderAttributes\Model\Config\Source
 */
class Position implements ArrayInterface
{
    const NONE = 0;
    const ADDRESS = 1;
    const SHIPPING_TOP = 2;
    const SHIPPING_BOTTOM = 3;
    const PAYMENT_TOP = 4;
    const PAYMENT_BOTTOM = 5;
    const ORDER_SUMMARY = 6;

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::NONE, 'label' => __('None')],
            ['value' => self::ADDRESS, 'label' => __('Shipping Address')],
            ['value' => self::SHIPPING_TOP, 'label' => __('Shipping Method Top')],
            ['value' => self::SHIPPING_BOTTOM, 'label' => __('Shipping Method Bottom')],
            ['value' => self::PAYMENT_TOP, 'label' => __('Payment Method Top')],
            ['value' => self::PAYMENT_BOTTOM, 'label' => __('Payment Method Bottom')],
            ['value' => self::ORDER_SUMMARY, 'label' => __('Order Summary')],
        ];
    }
}
