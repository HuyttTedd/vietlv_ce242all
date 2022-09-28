define([
    'jquery',
    'Magento_Ui/js/form/element/file-uploader',
], function ($, Uploader) {
    'use strict';

    return Uploader.extend({
        defaults: {
            listens: {
                isImageLoaded: 'hideUploader'
            },
        },

        initialize: function () {
            this._super();

            return this;
        },

        initObservable: function () {
            return this._super()
                .observe({
                    isImageLoaded: false,
                    addImageMode: false
                });
        },

        hideUploader: function () {
            this.visible(!this.isImageLoaded());
        },

        removeFile: function () {
            var _super = this._super,
                file = this.getFile();

            $.ajax({
                url: this.deleteUrl,
                showLoader: true,
                type: 'GET',
                data: {fileHash: file.name}
            }).done(function (response) {
                if (!response.error) {
                    _super.call(this, file);
                }
            }.bind(this));
        }
    });
});
