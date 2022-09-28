define(
    [
        'ko',
        'jquery',
        'uiElement',
        'uiRegistry',
        'Smartosc_CustomCheckout/js/action/start-place-order',
        'mage/translate'
    ],
    function (
        ko,
        $,
        Component,
        registry,
        startPlaceOrderAction
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Smartosc_CustomCheckout/onepage/place-order',
                defaultLabel: $.mage.__('Test Place Order'),
                visible: true,
                paymentsNamePrefix: 'checkout.steps.billing-step.payment.payments-list.',
                toolbarSelector: '.actions-toolbar',
                placeButtonSelector: '.action.primary',
                listens: {
                    'visible': 'onVisibilityChange'
                }
            },

            placeOrder: function () {
                startPlaceOrderAction();
            }
        });
    }
);
