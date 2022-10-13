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
    'Magento_Ui/js/form/element/abstract',
    'uiRegistry',
    'uiLayout',
    'mageUtils',
    'mage/translate',
    'Magento_Ui/js/modal/modal'
], function ($, Element, registry, layout, utils, $t, modal) {
    'use strict';

    return Element.extend({
        defaults: {
            visible: true,
            label: '',
            error: '',
            uid: utils.uniqueid(),
            disabled: false,
            links: {
                value: '${ $.provider }:${ $.dataScope }'
            }
        },

        /**
         * Calls 'initObservable' of parent
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super().observe('disabled visible value');

            $('body').on('click', '#mp-customers-grid-popup tbody tr', function () {
                var modalEl        = $('#mp-customers-grid-popup'),
                    customerId     = $(this).find('.col-id').text().trim(),
                    customerName   = $(this).find('.col-firstname').text().trim(),
                    customerEmail  = $(this).find('.col-email').text().trim(),
                    customerNameEl = $('#' + modalEl.data('id'));

                customerNameEl.val(customerName + ' (' + customerEmail + ')').trigger('change');
                customerNameEl.parent().siblings('.mp-specific-customer-id').find('input').val(customerId).trigger('change');
                modalEl.data('modal').closeModal();
            });

            return this;
        },

        choseCustomer: function (data, event) {
            var popupEl      = $('#mp-customers-grid-popup'),
                id           = $(event.target).attr('id'),
                customerIdEl = $(event.target).parent().siblings('.mp-specific-customer-id').find('input');

            if (popupEl.length) {
                popupEl.data('id', id).data('modal').openModal();
                if (customerIdEl.val()) {
                    $('.col-in_customer input[value=' + customerIdEl.val() + ']').prop('selected', true);
                }

                return;
            }
            $.ajax({
                type: 'POST',
                url: $(event.target).data('url'),
                data: {form_key: window.FORM_KEY},
                showLoader: true
            }).done(function (response) {
                var options = {
                    type: 'popup',
                    responsive: true,
                    innerScroll: true,
                    title: $t('Select Customer'),
                    buttons: []
                }, modalEl, popup;

                $('body').append('<div id="mp-customers-grid-popup" style="display: none">' + response + '</div>');
                modalEl = $('#mp-customers-grid-popup');

                modalEl.data('id', id).trigger('contentUpdated');
                popup = modal(options, modalEl);

                modalEl.on('modalopened', function () {
                    var customerNameEl = $('#' + $(this).data('id')),
                        customerId     = customerNameEl.parent().siblings('.mp-specific-customer-id').find('input').val();

                    modalEl.find('.radio[name="customer_id"][value="' + customerId + '"]').prop('checked', true);
                });
                modalEl.on('modalclosed', function () {
                    $(this).find('input[type="radio"]').each(function () {
                        $(this).prop('checked', false);
                    });
                });
                popup.openModal();
            });
        }
    });
});
