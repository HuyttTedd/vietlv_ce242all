define([
    'jquery',
    'Amasty_GiftCard/js/actions/notification'
], function ($, notification) {
    'use strict';

    return function (onlyImage) {
        var deferredSave = $.Deferred();
        notification(deferredSave, onlyImage);

        $.when(deferredSave).done(function () {
            this.fileUploader().removeFile();
            if (onlyImage) {
                this.addImageMode(onlyImage);
                this.isDesignMode(false);
            } else {
                this.image().removeAllChildren();
            }
        }.bind(this));
    };
});
