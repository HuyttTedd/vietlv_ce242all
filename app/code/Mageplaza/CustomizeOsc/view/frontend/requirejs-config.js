/* jshint browser:true jquery:true */
var amasty_mixin_enabled = !window.amasty_checkout_disabled,
    config;

config = {
    'map': { '*': {} },
    config: {
        mixins: {
            'Magento_Checkout/js/model/step-navigator': {
                'Mageplaza_CustomizeOsc/js/model/step-navigator-mixin': amasty_mixin_enabled
            },
            'Magento_Checkout/js/view/summary/cart-items': {
                'Mageplaza_CustomizeOsc/js/view/summary/cart-items-mixin': amasty_mixin_enabled
            }
        }
    }
};
