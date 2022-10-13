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
    'underscore',
    'Magento_Ui/js/form/form',
    'Mageplaza_OrderAttributes/js/model/order-attributes-data',
    'uiRegistry',
], function (ko, $, _, Component, checkoutData, registry) {
    return Component.extend({
        initialize: function () {
            this._super();

            var self = this;

            registry.async('mpOrderAttributesCheckoutProvider')(function (checkoutProvider) {
                var scopeCheckoutData = checkoutData.getData(self.scope);

                if (scopeCheckoutData) {
                    checkoutProvider.set(
                        self.scope,
                        $.extend({}, checkoutProvider.get(self.scope), scopeCheckoutData)
                    );
                }
                checkoutProvider.on(self.scope, function (scopeData) {
                    checkoutData.setData(self.scope, scopeData);
                });
            });
        },
    });
});
