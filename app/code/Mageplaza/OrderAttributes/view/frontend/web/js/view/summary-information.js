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
    'underscore',
    'uiComponent',
    'uiRegistry',
    'Magento_Checkout/js/model/step-navigator'
], function ($, _, Component, registry, stepNavigator) {
    'use strict';

    return Component.extend({
        defaults: {
            deps: 'mpOrderAttributesCheckoutProvider',
            scopes: [
                'mpShippingAddressNewAttributes',
                'mpShippingAddressAttributes',
                'mpShippingMethodTopAttributes',
                'mpShippingMethodBottomAttributes'
            ]
        },

        isVisible: function () {
            var steps       = stepNavigator.steps(),
                paymentStep = _.where(steps, {'code': 'payment'});

            return paymentStep.length && paymentStep[0].isVisible() && this.getOrderAttributes().length;
        },

        getOrderAttributes: function () {
            var self       = this,
                attributes = [];

            _.each(this.scopes, function (scope) {
                var container = registry.filter('scope = ' + scope);
                if (container.length) {
                    _.each(container[0].elems(), function (elem) {
                        if (elem.value() && elem.visible()) {
                            var item = self.getAttributeData(elem);
                            if (item.hasOwnProperty('value')) {
                                attributes.push(item);
                            }
                        }
                    });
                }
            });

            return attributes;
        },

        getAttributeData: function (elem) {
            var item = {'label': elem.label, 'url': '', 'preview': '', 'field_type': elem.fieldType};

            switch (elem.fieldType) {
                case 'select':
                    item['value'] = elem.indexedOptions[elem.value()].label;
                    break;
                case 'multiselect':
                    _.each(elem.value(), function (option) {
                        if(option) {
                            if (item['value'] === undefined) {
                                item['value'] = elem.indexedOptions[option].label;
                            } else {
                                item['value'] += ', ' + elem.indexedOptions[option].label;
                            }
                        }
                    });
                    break;
                case 'file':
                    if (elem.value().length) {
                        item['preview'] = elem.value()[0]['previewType'];
                        item['value']   = elem.value()[0]['name'];
                        item['url']     = elem.value()[0]['url'];
                    }
                    break;
                default:
                    item['value'] = elem.value();
                    break;
            }

            return item;
        }
    });
});
