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
    'Mageplaza_Customize/js/model/customize-attributes'
], function ($, _, registry, wrapper, checkoutData) {
    'use strict';

    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, function (originalAction, paymentData, messageContainer) {
            var dataArray = _.values(checkoutData.getData()),
                attributes = {},
                extension_attributes = {};

            _.each(dataArray, function (data) {
                _.extend(attributes, data);
            });

            paymentData.extension_attributes = {
                mpCustomizeAttributes: attributes
            };

            return originalAction(paymentData, messageContainer);
        });
    };
});
