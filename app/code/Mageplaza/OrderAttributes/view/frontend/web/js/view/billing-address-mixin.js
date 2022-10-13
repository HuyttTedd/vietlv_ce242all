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

define([
    'ko',
    'Magento_Checkout/js/model/quote'
], function (ko, quote) {
    'use strict';
    return function (BillingAddressComponent) {
        return BillingAddressComponent.extend({
            defaults: {
                detailsTemplate: 'Mageplaza_OrderAttributes/billing-address/details'
            },

            currentBillingAddressStreet: ko.computed(function () {
                if(quote.billingAddress() && quote.billingAddress().street){
                    return quote.billingAddress().street.slice(0);
                }

                return [];
            })
        })
    }
});
