// Checkout layout options model
define([
    'ko',
    'uiRegistry',
    'uiLayout'
], function (ko, registry, layout) {
    'use strict';

    const MAPPING_BLOCK_NAME = {
            shipping_address: 'checkout.steps.shipping-step.shippingAddress',
            shipping_method: 'checkout.steps.shipping-step.shippingAddress',
            delivery: 'checkout.steps.shipping-step.amcheckout-delivery-date',
            payment_method: 'checkout.steps.billing-step',
            summary: 'checkout.sidebar',
            additional_checkboxes: 'checkout.sidebar.additional.checkboxes'
        }

    return {
        containerClassNames: ko.observable(''),
        selectedLayout: [],
        checkoutDesign: '',
        checkoutLayout: '',
        checkoutBlocks: {},
        mainAdditionalClasses: '',

        /**
         * Comment
         */
        getCheckoutBlock: function (blockName) {
            var requestComponent = this.checkoutBlocks[blockName]
                || this.requestComponent(MAPPING_BLOCK_NAME[blockName]);

            return requestComponent;
        },

        /**
         * Comment
         */
        requestComponent: function (name) {
            var observable = ko.observable();

            registry.get(name, function (summary) {
                observable(summary);
            });

            this.checkoutBlocks[name] = observable;

            return observable;
        }
    };


});
