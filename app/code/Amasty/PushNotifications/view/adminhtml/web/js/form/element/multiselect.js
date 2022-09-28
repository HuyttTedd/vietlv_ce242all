define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/multiselect'
], function ($, _, uiRegistry, select) {
    'use strict';

    return select.extend({
        setOptionDisable: function (option, item) {
            option.disabled = item.disabled;
        }
    });
});
