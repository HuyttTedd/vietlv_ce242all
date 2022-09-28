define([
    'jquery',
    'Magento_Ui/js/modal/modal-component'
], function ($, Modal) {
    'use strict';

    return Modal.extend({
        defaults: {
            width: null,
            height: null,
            pos_y: null,
            pos_x: null,
            custom_css: '',
            maxWidth: 580,
            maxHeight: 390,
            isImageSettings: false,
            links: {
                width: '${ $.provider }:${ $.dataScope }.width',
                height: '${ $.provider }:${ $.dataScope }.height',
                pos_y: '${ $.provider }:${ $.dataScope }.pos_y',
                pos_x: '${ $.provider }:${ $.dataScope }.pos_x',
                custom_css: '${ $.provider }:${ $.dataScope }.custom_css'
            },
            imports: {
                currentImageWidth: '${ $.provider }:data.width',
                currentImageHeight: '${ $.provider }:data.height',
            },
            listens: {
                pos_y: 'updateCurrentTop',
                pos_x: 'updateCurrentLeft'
            }
        },

        updateCurrentTop: function (pos_y) {
            var current = this.current();

            current.pos_y = pos_y;
            this.current(current);
        },

        updateCurrentLeft: function (pos_x) {
            var current = this.current();

            current.pos_x = pos_x;
            this.current(current);
        },

        initObservable: function () {
            return this._super()
                .observe([
                    'width',
                    'height',
                    'pos_y',
                    'pos_x',
                    'custom_css'
                ])
                .observe({
                    hasError: false,
                    current: {
                        width: this.width(),
                        height: this.height(),
                        pos_y: this.pos_y(),
                        pos_x: this.pos_x(),
                        custom_css: this.custom_css(),
                    },
                });
        },

        openModal: function () {
            this.hasError(false);

            return this._super();
        },

        update: function () {
            if (!this.validate()) {
                return false;
            }

            this.closeModal();
            this.updateValues();
        },

        updateValues: function () {
            Object.keys(this.current()).forEach(function (key) {
                var newValue = key === 'custom_css' ? this.current()[key] : Number(this.current()[key]);

                this[key](newValue);
            }.bind(this));
        },

        validate: function () {
            var options = this.current();

            if (options.width > this.maxWidth || options.height > this.maxHeight) {
                this.hasError(true);

                return false;
            }

            if (!this.isImageSettings) {
                this.hasError(this.validateElement(options));

                return !this.hasError();
            }

            this.hasError(false);

            return true;
        },

        validateElement: function (options) {
            return this.validateLessThanZero(options)
                || +options.width > this.currentImageWidth
                || +options.height > this.currentImageHeight
                || +options.pos_x > this.currentImageWidth - options.width
                || +options.pos_y > this.currentImageHeight - options.height;
        },

        validateLessThanZero: function (options) {
            return Object.keys(options).some(function (key) {
                if (options[key] === 'custom_css') {
                    return false;
                }

                return options[key] < 0;
            });
        }
    });
});
