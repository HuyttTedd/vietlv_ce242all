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
 * @package     Mageplaza_ExtraFee
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'mage/translate',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Smartosc_ExtraFee/js/model/extra-fee-service'
], function ($, ko, Component, quote, $t, additionalValidators, extraFeeService) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Smartosc_ExtraFee/checkout/extra-fee-shipping'
        },

        extraFeeData: extraFeeService.getExtraFeeData(),

        initialize: function () {
            this._super();
        }
    });
});