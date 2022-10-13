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
 * @package     Mageplaza_BetterTierPrice
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    var widgetMixin = {
        _getNewPrices: function () {
            var $widget = this,
                optionPriceDiff = 0,
                allowedProduct = this._getAllowedProductWithMinPrice(this._CalcProducts()),
                optionPrices = this.options.jsonConfig.optionPrices,
                basePrice = parseFloat(this.options.jsonConfig.prices.basePrice.amount),
                optionFinalPrice,
                newPrices;

            if (!_.isEmpty(allowedProduct)) {
                optionFinalPrice = parseFloat(optionPrices[allowedProduct].finalPrice.amount);
                optionPriceDiff = optionFinalPrice - basePrice;
            }

            if (optionPriceDiff !== 0 || typeof this.options.jsonConfig.optionPrices[allowedProduct] !== 'undefined') {
                newPrices  = this.options.jsonConfig.optionPrices[allowedProduct];
            } else {
                newPrices = $widget.options.jsonConfig.prices;
            }

            return newPrices;
        }
    };

    return function (parentWidget) {
        $.widget('mage.SwatchRenderer', parentWidget, widgetMixin);

        return $.mage.SwatchRenderer;
    };
});
