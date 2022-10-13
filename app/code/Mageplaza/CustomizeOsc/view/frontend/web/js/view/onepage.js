define(
    [
        'jquery',
        'underscore',
        'uiComponent',
        'ko',
        'uiRegistry',
        'consoleLogger',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/action/select-billing-address',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Mageplaza_CustomizeOsc/js/model/one-step-layout'
    ],
    function (
        $,
        _,
        Component,
        ko,
        registry,
        consoleLogger,
        customer,
        selectBillingAddress,
        quote,
        paymentValidatorRegistry,
        paymentMethodConverter,
        paymentService,
        checkoutDataResolver,
        oneStepLayout
    ) {
        'use strict';

        return Component.extend({
            /** @inheritdoc */
            initialize: function () {
                this._super();

                this.initCheckoutLayout();
            },

            /**
             * Comment
             */
            getOneStepModel: function () {
                return oneStepLayout;
            },

            getTestName: function () {
                return 'oke con de 3';
            },

            /**
             * Init checkout layout by quote type
             * @returns {void}
             */
            initCheckoutLayout: function () {
                if (!quote.isVirtual()) {
                    oneStepLayout.selectedLayout = window.checkoutConfig.checkoutCustom;
                } else {
                    oneStepLayout.selectedLayout = oneStepLayout.getVirtualLayout();
                }
            },

        });
    }
);
