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
    'underscore',
    'jquery',
    'Magento_Ui/js/form/element/select',
    'uiRegistry',
    'uiLayout',
    'mageUtils',
    'mage/backend/notification'
], function (_, $, Element, registry, layout, utils, notification) {
    'use strict';

    /**
     * Parses incoming options, considers options with undefined value property
     *     as caption
     *
     * @param  {Array} nodes
     * @param captionValue
     * @return {Object}
     */
    function parseOptions(nodes, captionValue) {
        var caption = 'undefined',
            value;

        nodes = _.map(nodes, function (node) {
            value = node.value;

            if (value === null || value === captionValue) {
                if (_.isUndefined(caption)) {
                    caption = node.label;
                }
            } else {
                return node;
            }
        });

        return {
            options: _.compact(nodes),
            caption: _.isString(caption) ? caption : false
        };
    }

    /**
     * Recursively set to object item like value and item.value like key.
     *
     * @param {Array} data
     * @param {Object} result
     * @returns {Object}
     */
    function indexOptions(data, result) {
        var value;

        result = result || {};

        data.forEach(function (item) {
            value = item.value;

            if (Array.isArray(value)) {
                indexOptions(value, result);
            } else {
                result[value] = item;
            }
        });

        return result;
    }

    return Element.extend({
        defaults: {
            visible: true,
            label: '',
            error: '',
            uid: utils.uniqueid(),
            disabled: false,
            links: {
                value: '${ $.provider }:${ $.dataScope }'
            },
            editModalContainer: 'product_form.product_form.mp_tier_group_modal_edit.mp_tier_group_modal_container',
            tierPriceName: 'product_form.product_form.advanced_pricing_modal.advanced-pricing.tier_price'
        },
        initObservable: function () {
            this._super();
            this.selectedOptionObs();
            this.updateOptionsObs();
            return this;
        },
        selectedOptionObs: function () {
            var self = this;

            this.value.subscribe(function (newValue) {
                var result         = parseOptions(self.options(), ''),
                    indexedOptions = indexOptions(result.options, null),
                    editModalIndex = self.options().indexOf(indexedOptions[newValue]),
                    editButton     = registry.get(self.parentName + '.mp_edit_tier_group'),
                    tierPriceEl    = registry.get(self.tierPriceName),
                    data, editModalIndexEl, editModalNameEl, editModalPriceEl;

                if (newValue === ' ') {
                    editButton.visible(false);
                    if (!self.currentTierPrice) {
                        self.currentTierPrice = tierPriceEl.recordData();
                    } else {
                        self.reloadTierPrice(tierPriceEl, self.currentTierPrice);
                    }
                } else {
                    data             = JSON.parse(newValue);
                    editModalIndexEl = registry.get(self.editModalContainer + '.mp_tier_group_index');
                    editModalNameEl  = registry.get(self.editModalContainer + '.mp_tier_group_name');
                    editModalPriceEl = registry.get(self.editModalContainer + '.mp_tier_group_price_value');

                    editButton.visible(true);
                    editModalIndexEl.value(editModalIndex);
                    editModalNameEl.value(indexedOptions[newValue].label);
                    self.reloadTierPrice(editModalPriceEl, data);
                    self.reloadTierPrice(tierPriceEl, data);
                }
            });
        },
        reloadTierPrice: function (el, data) {
            el.recordData(data);
            el.reload();
            el._sort();
        },
        updateOptionsObs: function () {
            var self = this;

            this.options.subscribe(function () {
                $.ajax({
                    url: self.updateTierGroupUrl,
                    method: 'POST',
                    data: {tierGroup: JSON.stringify(self.options()), form_key: window.FORM_KEY},
                    showLoader: true,
                    success: function (res) {
                        if (res.error_mes) {
                            notification().add({
                                error: true,
                                message: res.error_mes,
                                insertMethod: function (message) {
                                    var $wrapper = $('<div/>').html(message);

                                    $('.page-main-actions').after($wrapper);
                                }
                            });
                        }
                    }
                });
            });
        }
    });
});
