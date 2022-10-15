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
    'mage/translate',
    'Magento_Catalog/js/price-utils'
], function ($, _, $t,priceUtils) {
    "use strict";

    $.widget('mageplaza.betterchangeqty', {
        _create: function () {
            var self = this,
                container = this.element.parent(),
                productId = container.find('[data-role="priceBox"]').attr('data-product-id'),
                finalPrice = container.find('[data-price-type="finalPrice"]').attr('data-price-amount'),
                qtyBox = this.element.find('.mp-better-qty-box'),
                qtyInput = this.element.find('.mp-better-qty-input');

            this.bindQtyOption(qtyInput, qtyBox, container);

            qtyBox.trigger('change');
            qtyInput.trigger('change');

            /** reload option label on changing configurable options */
            container.on('change', '.swatch-input', function () {
                var options = _.object(self.options.optionMap, []),
                    product;

                $(this).parents('.product-item-info')
                .find('.swatch-opt-' + productId + ' .swatch-attribute[option-selected]').each(function () {
                    var attributeId = $(this).attr('attribute-id');

                    options[attributeId] = $(this).attr('option-selected');
                });

                product = _.findWhere(self.options.configurable, options) || {};

                qtyInput = self.getQtyOptionValue(qtyInput, product, container);

                self.getQtyOptionLabel(qtyInput, product, finalPrice);
            });
        },

        bindQtyOption: function (qtyInput, qtyBox, container) {
            container.on('change', qtyBox.selector, function () {
                var qty = $(this).val(),
                    addButton = $(this).parents('.product-item-info').find('[data-role="tocart-form"] .tocart');

                if (qty !== '' && qty >0) {
                    addButton.removeAttr('disabled');
                    $(this).parent().find('.mage-error').remove();
                    $(this).parents('.product-item-info').find('[data-role="tocart-form"] input[name="qty"]').val(qty);
                } else {
                    $(this).parent().append('<div for="qty" generated="true" class="mage-error" id="qty-error"' +
                        ' style="display: block;">'+$t('Please enter a quantity greater than 0.')+'</div>');
                    addButton.attr('disabled','true');
                }
            });

            container.on('change', qtyInput.selector, function () {
                var input = $(this).next().filter('input[type="number"]');

                if ($(this).val() === '0') {
                    input.show();
                    input.prop('disabled', false);
                } else {
                    input.hide();
                    input.prop('disabled', true);
                }

                $(this).parents('.product-item-info').find('[data-role="tocart-form"] input[name="qty"]')
                .val($(this).val());
            });
        },

        getQtyOptionValue: function (qtyInput, product, container) {
            var configurableQty = _.isEmpty(product) ? this.options.configurableQty : product['qty'],
                openInput = qtyInput.children('option[value="0"]'),
                selected = qtyInput.val(),
                step = this.options.step,
                limit;

            var qtyInputHtml = '';

            if (_.isArray(step)) {
                _.each(step, function (value) {
                    if (value <= configurableQty) {
                        qtyInputHtml += '<option value="' + value + '"></option>';
                    }
                });
            } else {
                limit = this.options.limit * step;
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

            qtyInput
            .replaceWith('<select name="qty" class="' + qtyInput.attr('class') + '">' + qtyInputHtml + '</select>');

            qtyInput = container.find(qtyInput.selector);

            if (selected && qtyInput.children('option[value="' + selected + '"]').length) {
                qtyInput.val(selected);
            }

            qtyInput.trigger('change');

            return qtyInput;
        },

        getQtyOptionLabel: function (qtyInput, product, finalPrice) {
            var self = this;


            qtyInput.children('option').each(function () {
                var value = parseFloat($(this).val()),
                    price,
                    title,
                    percent,
                    productPrice;

                if (!value) {
                    return;
                }

                price = product['price'] || finalPrice;
                percent = 1;

                if (value === 1) {
                    title = self.options.optTmpl;
                } else {
                    title = self.options.optTmplMulti;

                    if (product['tier'] && product['tier'].length) {
                        $.each(product['tier'], function (index, item) {
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

                productPrice = Math.round(price * percent * 100) / 100;

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
