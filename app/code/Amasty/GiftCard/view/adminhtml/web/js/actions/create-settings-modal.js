define([
    'jquery',
    'mageUtils',
    'uiLayout',
    'mage/translate'
], function ($, utils, layout) {
    'use strict';

    return function (name, elementTitle, isImage, additionalProps) {
        var component = 'Amasty_GiftCard/js/form/components/modal',
            partTitle = isImage ? $.mage.__('Edit') : $.mage.__('Edit Element'),
            title = partTitle + ' "' + elementTitle + '"',
            field;

        if (typeof additionalProps !== 'object') {
            additionalProps = {};
        }

        field = utils.extend(additionalProps, {
            name: this.name + '.' + name,
            component: component,
            provider: this.provider,
            dataScope: this.dataScope,
            template: 'Amasty_GiftCard/modal',
            type: 'popup',
            responsive: true,
            isImageSettings: !!isImage,
            options: {
                title: title,
                modalClass: 'amgcard-modal-container',
            },
            innerScroll: true
        });

        layout([field]);
        this.insertChild(field.name);

        return field.name;
    };
});
