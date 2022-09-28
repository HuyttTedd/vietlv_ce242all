define([
    'jquery',
    'uiComponent',
    'Amasty_GiftCard/js/actions/create-draggable-element',
    'Amasty_GiftCard/js/model/draggable',
    'mage/translate',
    'jquery/ui',
], function ($, Component, createElement, model) {
    'use strict';

    return Component.extend({
        defaults: {
            imports: {
                elements: '${ $.provider }:data.elements',
                imageWidth: '${ $.provider }:data.width',
                imageHeight: '${ $.provider }:data.height'
            },
            modules: {
                image: '${ $.parentName }.image_container.image_elements'
            },
            css: {
                used: '-used'
            }
        },

        initObservable: function () {
            return this._super()
                .observe({
                    isDesignMode: false
                });
        },

        initDroppable: function (element) {
            model.initDroppable(this, element);
        },

        initDnd: function (element, item) {
            $(element).draggable({
                helper: 'clone',
                revert: 'invalid'
            }).data({
                item: item
            });
        },

        onDropEnd: function (event, ui) {
            var code = ui.draggable.data('code');

            if (ui.draggable.data('amgcardElement')) {
                model.restoreElement(code);
                this.image().removeElement(code);
            }
        },

        createDragElement: function (data, element) {
            var positions = {
                    pos_x: this.imageWidth / 2 - data.default.width / 2,
                    pos_y: this.imageHeight / 2 - data.default.height / 2
                };

            createElement.call(this.image(), data, positions);
            $(element).addClass(this.css.used);
        },

        isElementUsed: function (code) {
            var elements = this.source.data.image_elements;

            if (elements === undefined) {
                return false;
            }

            return !!elements[code];
        }
    });
});
