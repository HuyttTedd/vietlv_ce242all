<?php

namespace Mageplaza\CustomizeOsc\Model;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Add checkout blocks config to checkout config
 * @since 3.0.0
 */
class ConfigProvider implements ConfigProviderInterface
{
    const CONFIG_KEY = 'checkoutCustom';

    /**
     * Retrieve assoc array of checkout configuration
     *
     * @return array
     */
    public function getConfig()
    {
        //pattern
//        $configLayout = [
//            [
//            { "name": "shipping_address", title: "Shipping Address" },
//            { name: "shipping_method", title: "Shipping Method" },
//            { name: "delivery", title: "Delivery" },
//          ],
//          [
//            { "name": "payment_method", title: "Payment Method" },
//            { name: "summary", title: "Order Summary" },
//          ]
//        ];

        $configLayout = [
            [
                [ "name"=>"summary", "title"=>"Order Summary" ],
            ]
        ];

        return [
            static::CONFIG_KEY => $configLayout
        ];
    }
}
