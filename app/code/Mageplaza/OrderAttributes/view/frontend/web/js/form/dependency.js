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
    'jquery',
    'uiRegistry',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/quote'
], function ($, registry, addressList, quote) {
    'use strict';

    var attributeDepend = window.checkoutConfig.mpOaConfig.attributeDepend,
        shippingDepend = window.checkoutConfig.mpOaConfig.shippingDepend,
        fieldset = [
            '',
            'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.mpOrderAttributes',
            'checkout.steps.shipping-step.shippingAddress.before-shipping-method-form.mpOrderAttributes',
            'checkout.steps.shipping-step.shippingAddress.mpOrderAttributes',
            'checkout.steps.billing-step.payment.beforeMethods.mpOrderAttributes',
            'checkout.steps.billing-step.payment.afterMethods.mpOrderAttributes',
            'checkout.sidebar.summary.itemsAfter.mpOrderAttributes'
        ],
        multiselectAttr = [
            'Magento_Ui/js/form/element/multiselect',
            'Mageplaza_OrderAttributes/js/form/element/checkboxes'
        ];

    if (addressList().length) {
        fieldset[1] = 'checkout.steps.shipping-step.shippingAddress.before-form.mpOrderAttributes';
    }

    if (window.checkoutConfig.mpOaConfig.isOscPage) {
        fieldset[6] = 'checkout.sidebar.place-order-information-left.addition-information.mpOrderAttributes';
    }

    function isShippingDepend(elem) {
        var result = false,
            method;

        if (quote.shippingMethod()) {
            method = quote.shippingMethod().carrier_code + '_' + quote.shippingMethod().method_code;
        }

        $.each(shippingDepend, function (index, attribute) {
            if (attribute.attribute_code === elem.index) {
                var dependMethod = attribute.shipping_depend.split(',');
                result = $.inArray(method, dependMethod) === -1;
                return false;
            }
        });

        return result;
    }

    return {
        initialize: function () {
            this._super();

            if (attributeDepend.length) {
                this.checkDependency();
            }

            return this;
        },

        onUpdate: function () {
            this._super();

            if (attributeDepend.length) {
                this.checkDependency();
            }
        },

        checkDependency: function () {
            var self = this,
                attrId;

            $.each(attributeDepend, function (index, attribute) {
                if (attribute.attribute_code === self.index) {
                    attrId = attribute.attribute_id;
                }
            });

            $.each(attributeDepend, function (index, attribute) {
                if (attribute.field_depend === attrId && attribute.value_depend) {
                    registry.async(fieldset[attribute.position] + '.' + attribute.attribute_code)(function (elem) {
                        var valueDepend = attribute.value_depend.split(',');

                        if ($.inArray(attrId + '_' + self.value(), valueDepend) === -1 || isShippingDepend(elem)) {
                            elem.hide();
                            elem.disabled(true);
                            elem.value($.inArray(elem.component, multiselectAttr) !== -1 ? [null] : null);
                        } else {
                            elem.show();
                            elem.disabled(false);
                            elem.value($.inArray(elem.component, multiselectAttr) !== -1 ? [] : null);
                        }
                    });
                }
            });
        },
    };
});
