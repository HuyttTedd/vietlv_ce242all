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
 * @package     Mageplaza_BetterChangeQty
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'underscore',
    'Magento_Catalog/js/price-utils'
], function ($, _, priceUtils) {
    "use strict";

    $.widget('mageplaza.betterchangeqty', {
        _create: function () {
            var self = this,
                body = $('body'),
                container = this.element.parent().parent(),
                finalPrice = this.options.finalPrice,
                qtyInput = container.find('.mp-better-qty-input'),
                priceBoxElm = container.parent().parent().find('[data-role=priceBox]');

            if (priceBoxElm.length) {
                priceBoxElm = $(priceBoxElm[0]);
            }

            this.bindQtyOption(qtyInput, container);

            qtyInput.trigger('change');

            /** reload option label on changing customize or bundle options */
            priceBoxElm.on('updatePrice', function (event, data) {
                if ($('#giftcard-amount-input').length || (data && data.hasOwnProperty('prices'))) {
                    return;
                }

                var priceElm = body.find('[data-price-type="finalPrice"]');
                priceElm = priceElm.length ? $(priceElm[0]) : $(this);
                var price = parseFloat(priceElm.text().replace(/[^0-9.]+/g, ''));

                self.getQtyOptionLabel(qtyInput, self.options.tierPrices, price, finalPrice);
            });

            priceBoxElm.trigger('updatePrice');

            /** Magento EE Gift Card compatible */
            container.on('change', '#giftcard-amount-input', function () {
                self.getQtyOptionLabel(qtyInput, self.options.tierPrices, +$(this).val(), finalPrice);
            });

            /** reload option label on changing configurable options */
            container.on('change', '.swatch-input', function () {
                var options = _.object(self.options.optionMap, []);

                $(this).parents('.swatch-opt').find('.swatch-attribute[option-selected]').each(function () {
                    var attributeId = $(this).attr('attribute-id');

                    options[attributeId] = $(this).attr('option-selected');
                });

                var product = _.findWhere(self.options.configurable, options) || {};

                qtyInput = self.getQtyOptionValue(qtyInput, product, container);

                self.getQtyOptionLabel(qtyInput, product['tier'], product['price'] || finalPrice, finalPrice);
            });
        },

        bindQtyOption: function (qtyInput, container) {
            container.on('change', qtyInput.selector, function () {
                var input = $(this).next().filter('input[type="number"]');
                if ($(this).val() === '0') {
                    input.show();
                    input.prop('disabled', false);
                } else {
                    input.hide();
                    input.prop('disabled', true);
                }
            });
        },

        getQtyOptionValue: function (qtyInput, product, container) {
            var configurableQty = _.isEmpty(product) ? this.options.configurableQty : product['qty'],
                openInput = qtyInput.children('option[value="0"]'),
                selected = qtyInput.val(),
                step = this.options.step;

            var qtyInputHtml = '';

            if (_.isArray(step)) {
                _.each(step, function (value) {
                    if (value <= configurableQty) {
                        qtyInputHtml += '<option value="' + i + '"></option>';
                    }
                });
            } else {
                var limit = this.options.limit * step;
                if (limit) {
                    configurableQty = Math.min(configurableQty, limit);
                }

                for (var i = step; i <= configurableQty; i += step) {
                    qtyInputHtml += '<option value="' + i + '"></option>';
                }
            }

            if (openInput.length) {
                qtyInputHtml += '<option value="' + openInput.val() + '">' + openInput.text() + '</option>';
            }

            qtyInput.replaceWith('<select name="qty" class="' + qtyInput.attr('class') + '">' + qtyInputHtml + '</select>');

            qtyInput = container.find(qtyInput.selector);

            if (selected && qtyInput.children('option[value="' + selected + '"]').length) {
                qtyInput.val(selected);
            }

            qtyInput.trigger('change');

            return qtyInput;
        },

        getQtyOptionLabel: function (qtyInput, tierPrices, price, finalPrice) {
            var self = this;

            qtyInput.children('option').each(function () {
                var value = parseFloat($(this).val());

                if (!value) {
                    return;
                }

                var title, percent = 1;

                if (value === 1) {
                    title = self.options.optTmpl;
                } else {
                    title = self.options.optTmplMulti;

                    if (tierPrices && tierPrices.length) {
                        $.each(tierPrices, function (index, item) {
                            if (value >= item['qty']) {
                                title = self.options.optTmplTier;
                                if (item['percent']) {
                                    percent = 1 - item['percent'] / 100;
                                } else {
                                    percent = (parseFloat(item['value']) + price - finalPrice) / price;
                                    // percent = item['value'] / price;
                                }
                            }
                        });
                    }
                }

                var productPrice = Math.round(price * percent * 100) / 100;

                title = title.replace('{{qty}}', value)
                    .replace('{{price}}', priceUtils.formatPrice(productPrice, self.options.format))
                    .replace('{{total}}', priceUtils.formatPrice(productPrice * value, self.options.format))
                    .replace('{{percent}}', Math.round(100 - percent * 100) + '%');
                $(this).text(title);
            });
        }
    });

    return $.mageplaza.betterchangeqty;
});
