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
    'Magento_Ui/js/modal/modal-component',
    'uiRegistry'
], function ($, Component, registry) {
    'use strict';

    return Component.extend({
        defaults: {
            tierGroupContainerName: 'product_form.product_form.advanced_pricing_modal.advanced-pricing.container_mp_tier_group'
        },
        initialize: function () {
            this._super();

            return this;
        },
        initObservable: function () {
            this._super();

            this.state.subscribe(function (newValue) {
                var groupNameEl = registry.get(this.name + '.mp_tier_group_modal_container.mp_tier_group_name');

                groupNameEl.disabled(!newValue);
                if (
                    this.name === 'product_form.product_form.mp_tier_group_modal' ||
                    this.name === 'mp_tier_price_update.mp_tier_price_update.mp_tier_group_modal'
                ) {
                    this.resetAddModalData();
                }
            }.bind(this));

            this.updateAttributeObs();

            return this;
        },
        updateAttributeObs: function(){
            registry.async('mp_tier_price_update.mp_tier_price_update.product_details.tier_price')(function (tierPrice) {
                tierPrice.recordData.subscribe(function (newVal) {
                    var dataEl = $('#attributes-edit-form').find('#mp-tier-price-data');

                    if(dataEl.length){
                        dataEl.val(JSON.stringify(newVal));
                    }else{
                        $('#attributes-edit-form').append('<input id="mp-tier-price-data" type="hidden" name="mpTierPriceData" value="'+JSON.stringify(newVal)+'"/>');
                    }
                });
            });
            registry.async('mp_tier_price_update.mp_tier_price_update.product_details.mp_specific_customer')(function (specificCustomer) {
                specificCustomer.recordData.subscribe(function (newVal) {
                    var dataEl = $('#attributes-edit-form').find('#mp-specific-customer-data');

                    if(dataEl.length){
                        dataEl.val(JSON.stringify(newVal));
                    }else{
                        $('#attributes-edit-form').append('<input id="mp-specific-customer-data" type="hidden" name="mpSpecificCustomer" value="'+JSON.stringify(newVal)+'"/>');
                    }
                });
            });
        },
        actionDone: function () {
            this.valid = true;
            this.elems().forEach(this.validate, this);
            if (this.valid) {
                if (this.name === this.parentName + '.mp_tier_group_modal') {
                    this.createNewTierGroup();
                } else {
                    this.saveEditTier();
                }

                this.closeModal();
            }
        },
        actionDelete: function () {
            var tierGroup      = registry.get(this.tierGroupContainerName + '.mp_tier_group_data'),
                editModalIndex = registry.get(this.name + '.mp_tier_group_modal_container.mp_tier_group_index'),
                options        = tierGroup.options();

            options.splice(editModalIndex.value(), 1);
            tierGroup.options(options);
            this.closeModal();
        },
        saveEditTier: function () {
            var editModalIndex      = registry.get(this.name + '.mp_tier_group_modal_container.mp_tier_group_index'),
                editModalName       = registry.get(this.name + '.mp_tier_group_modal_container.mp_tier_group_name'),
                editModalPriceValue = registry.get(this.name
                    + '.mp_tier_group_modal_container.mp_tier_group_price_value'),
                tierGroup           = registry.get(this.tierGroupContainerName + '.mp_tier_group_data'),
                groupData           = editModalPriceValue.recordData(),
                options             = tierGroup.options();

            options[editModalIndex.value()] = {label: editModalName.value(), value: JSON.stringify(groupData)};
            tierGroup.options(options);
        },
        createNewTierGroup: function () {
            var addModalName        = registry.get(this.name + '.mp_tier_group_modal_container.mp_tier_group_name'),
                name                = addModalName.value(),
                editModalPriceValue = registry.get(this.name
                    + '.mp_tier_group_modal_container.mp_tier_group_price_value'),
                groupData           = editModalPriceValue.recordData(),
                tierGroup           = registry.get(this.tierGroupContainerName + '.mp_tier_group_data');

            tierGroup.options.push({
                label: name,
                value: JSON.stringify(groupData)
            });
            this.resetAddModalData();
        },
        resetAddModalData: function () {
            var addModalName       = registry.get(this.name + '.mp_tier_group_modal_container.mp_tier_group_name'),
                addModalPriceValue = registry.get(this.name
                    + '.mp_tier_group_modal_container.mp_tier_group_price_value');

            addModalName.reset();
            addModalPriceValue.recordData([]);
            addModalPriceValue.reload();
            addModalPriceValue._sort();
        },
    });
});
