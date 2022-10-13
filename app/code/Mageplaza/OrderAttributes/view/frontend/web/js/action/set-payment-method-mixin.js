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
    'uiRegistry',
    'mage/utils/wrapper',
    'Mageplaza_OrderAttributes/js/model/order-attributes-data',
    'Magento_Checkout/js/model/quote'
], function ($, _, registry, wrapper, checkoutData, quote) {
    'use strict';

    return function (setPaymentMethodAction) {
        return wrapper.wrap(setPaymentMethodAction, function (originalAction, messageContainer) {
            var dataArray = _.values(checkoutData.getData()),
                attributes = {},
                extension_attributes = {};

            _.each(dataArray, function (data) {
                _.extend(attributes, data);
            });

            _.each(attributes, function (attribute, key) {
                var isFile = false;

                if (_.isArray(attribute)) {
                    _.each(attribute, function (value) {
                        if (_.isObject(value) && value['file']) {
                            isFile = true;
                            extension_attributes[key] = JSON.stringify(value);
                        }
                        return false;
                    });

                    if (!isFile) {
                        key = key.replace('[]', '');
                        attribute = attribute.length === 1 && attribute[0] === null ? null : attribute.join(',');
                    }
                }

                if (!isFile && attribute !== null) {
                    extension_attributes[key] = attribute;
                }
            });

            quote.paymentMethod().extension_attributes = {
                mpOrderAttributes: extension_attributes
            };

            return originalAction(messageContainer);
        });
    };
});
