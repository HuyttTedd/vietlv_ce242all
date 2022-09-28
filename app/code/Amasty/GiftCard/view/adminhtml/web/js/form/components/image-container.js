define([
    'uiComponent',
    'Amasty_GiftCard/js/actions/notification',
    'Amasty_GiftCard/js/actions/delete-image',
], function (Component, notification, deleteImage) {
    'use strict';

    return Component.extend({
        defaults: {
            listens: {
                isDesignMode: 'toggleButtonDeleteImage',
                imageFile: 'toggleButtonDeleteImage'
            },
            modules: {
                fileUploader: '${ $.name }.image',
                source: '${ $.dataProvider }',
                image: '${ $.name }.image_elements'
            }
        },

        initObservable: function () {
            return this._super()
                .observe({
                    isDesignMode: false,
                    imageFile: null,
                    isShowDeleteButton: false
                });
        },

        deleteImage: function () {
            deleteImage.call(this);
        },

        toggleButtonDeleteImage: function () {
            if (this.imageFile() && !this.isDesignMode()) {
                this.isShowDeleteButton(true);
            } else {
                this.isShowDeleteButton(false);
            }
        }
    });
});
