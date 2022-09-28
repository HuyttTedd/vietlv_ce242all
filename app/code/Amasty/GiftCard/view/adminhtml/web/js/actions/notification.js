define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'uiRegistry',
    'mage/translate'
], function ($, message, registry, $t) {
    'use strict';

    return function (deferred, onlyImage) {
        var generalText = $t('Do you really want to delete the image?'),
            text = onlyImage ? $t('This process cannot be undone.') : $t('All design configurations will be erased.'),
            modalConfig = {
            modalClass: 'amgcard-alert-container',
            buttons: [{
                text: $t('Yes'),
                class: 'amacard-button -dark',
                click: function () {
                    deferred.resolve();
                    this.closeModal();
                }
            }, {
                text: $t('No'),
                class: 'amacard-button -light',
                click: function () {
                    deferred.reject();
                    this.closeModal();
                }
            }],
            content: generalText + ' ' + text
        };

        message(modalConfig);
    };
});
