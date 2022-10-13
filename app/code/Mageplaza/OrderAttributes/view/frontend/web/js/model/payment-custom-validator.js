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

define(
    [
        'jquery',
        'underscore',
        'uiRegistry',
        'Magento_Customer/js/model/address-list',
        'mage/validation'
    ],
    function ($, _, registry, addressList) {
        'use strict';

        var scopes = [
            '',
            '', //shipping address
            '', //shipping method top
            '', //shipping method bottom
            'mpPaymentMethodTopAttributes',
            'mpPaymentMethodBottomAttributes',
            'mpOrderSummaryAttributes'
        ];

        if (window.checkoutConfig.mpOaConfig.isOscPage) {
            if (!window.checkoutConfig.quoteData.is_virtual) {
                scopes[1] = 'mpShippingAddressAttributes';
                scopes[2] = 'mpShippingMethodTopAttributes';
                scopes[3] = 'mpShippingMethodBottomAttributes';

                if (addressList().length) {
                    scopes[1] = 'mpShippingAddressNewAttributes';
                }
            }

            scopes[6] = 'mpOrderSummaryOscAttributes';
        }

        return {
            validate: function () {
                var source = registry.get('mpOrderAttributesCheckoutProvider'),
                    result = true;

                _.each(scopes, function (scope) {
                    if (scope && source.get(scope)) {
                        source.set('params.invalid', false);
                        source.trigger(scope + '.data.validate');
                        if (source.get('params.invalid')) {
                            result = false;
                            return false;
                        }
                    }
                });

                return result;
            }
        };
    }
);
