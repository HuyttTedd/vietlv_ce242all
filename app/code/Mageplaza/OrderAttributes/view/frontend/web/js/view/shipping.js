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
    'jquery',
    'uiRegistry',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/quote'
], function (ko, $, registry, addressList, quote) {
    'use strict';

    return function (Component) {
        var attributeDepend = window.checkoutConfig.mpOaConfig ? window.checkoutConfig.mpOaConfig.attributeDepend : [],
            shippingDepend  = window.checkoutConfig.mpOaConfig ? window.checkoutConfig.mpOaConfig.shippingDepend : [],
            isOscPage       = window.checkoutConfig.mpOaConfig ? window.checkoutConfig.mpOaConfig.isOscPage : [],
            fieldset        = [
                '',
                'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset.mpOrderAttributes',
                'checkout.steps.shipping-step.shippingAddress.before-shipping-method-form.mpOrderAttributes',
                'checkout.steps.shipping-step.shippingAddress.mpOrderAttributes',
                'checkout.steps.billing-step.payment.beforeMethods.mpOrderAttributes',
                'checkout.steps.billing-step.payment.afterMethods.mpOrderAttributes',
                'checkout.sidebar.summary.itemsAfter.mpOrderAttributes'
            ],
            scopes          = [
                '',
                'mpShippingAddressAttributes',
                'mpShippingMethodTopAttributes',
                'mpShippingMethodBottomAttributes'
            ];

        if (addressList().length) {
            fieldset[1] = 'checkout.steps.shipping-step.shippingAddress.before-form.mpOrderAttributes';
            scopes[1]   = 'mpShippingAddressNewAttributes';
        }

        if (isOscPage) {
            fieldset[6] = 'checkout.sidebar.place-order-information-left.addition-information.mpOrderAttributes';
        }

        function checkShippingDepend (method) {
            $.each(shippingDepend, function (index, attribute) {
                $.each(fieldset, function (key, value) {
                    registry.async(value)(function (container) {
                        if (!container) {
                            return true;
                        }

                        $.each(container._elems, function (key, value) {
                            registry.async(value)(function (elem) {
                                if (!elem) {
                                    return true;
                                }

                                if (isShippingDepend(elem) && attribute.attribute_code === elem.index) {
                                    var content      = typeof tinymce !== 'undefined' ? tinymce.get(elem.uid) : null;
                                    var dependMethod = attribute.shipping_depend.split(',');
                                    var textareaComponent = 'Mageplaza_OrderAttributes/js/form/element/textarea';
                                    var checkboxComponent = 'Mageplaza_OrderAttributes/js/form/element/checkboxes';

                                    if ($.inArray(method, dependMethod) === -1 || isAttributeDepend(elem)) {
                                        elem.hide();
                                        elem.disabled(true);
                                        elem.value(elem.component === checkboxComponent ? [] : null);
                                        if (elem.component === textareaComponent && content !== null) {
                                            content.setContent('');
                                        }
                                    } else {
                                        elem.show();
                                        elem.value(elem.default);
                                        if (elem.component === textareaComponent && content !== null) {
                                            content.setContent(elem.default);
                                        }
                                        elem.disabled(false);
                                    }
                                }
                            })
                        })
                    });
                });
            });
        }

        function isShippingDepend (elem) {
            var result = false;

            $.each(shippingDepend, function (index, attribute) {
                if (attribute.attribute_code === elem.index) {
                    result = true;
                    return false;
                }
            });

            return result;
        }

        function isAttributeDepend (elem) {
            var result = false;

            $.each(shippingDepend, function (index, attribute) {
                if (attribute.attribute_code === elem.index && attribute.value_depend) {
                    var parentElem = getAttributeById(attribute.field_depend);
                    if (parentElem) {
                        var dependValue = attribute.value_depend.split(',');
                        result          = ($.inArray(attribute.field_depend + '_' + parentElem.value(), dependValue) === -1);
                        return false;
                    }
                }
            });

            return result;
        }

        function getAttributeById (id) {
            var result = false;

            $.each(attributeDepend, function (index, attribute) {
                if (attribute.attribute_id === id) {
                    result = registry.get(fieldset[attribute.position] + '.' + attribute.attribute_code);
                    return false;
                }
            });

            return result;
        }

        function getSelectedShippingMethod () {
            var method = window.checkoutConfig.selectedShippingMethod;

            if (method) {
                method = method.carrier_code + '_' + method.method_code;
            } else if (window.checkoutConfig.selectedShippingRate) {
                method = window.checkoutConfig.selectedShippingRate;
            }

            return method;
        }

        checkShippingDepend(getSelectedShippingMethod());

        return Component.extend({
            initObservable: function () {
                this._super();

                quote.shippingMethod.subscribe(function (method) {
                    var shippingMethod;
                    if (method && method.carrier_code && method.method_code) {
                        shippingMethod = method.carrier_code + '_' + method.method_code;
                    }
                    checkShippingDepend(shippingMethod);
                });

                return this;
            },

            validateShippingInformation: function () {
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

                return result ? this._super() : false;
            }
        });
    }
});
