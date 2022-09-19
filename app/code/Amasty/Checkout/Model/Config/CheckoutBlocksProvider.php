<?php

namespace Amasty\Checkout\Model\Config;

class CheckoutBlocksProvider
{
    /**
     * @return array
     */
    public function getDefaultBlockTitles()
    {
        return [
            'shipping_address' => __('Shipping Address'),
            'shipping_method' => __('Shipping Method'),
            'delivery' => __('Delivery'),
            'payment_method' => __('Payment Method'),
            'summary' => __('Order Summary'),
        ];
    }
}
