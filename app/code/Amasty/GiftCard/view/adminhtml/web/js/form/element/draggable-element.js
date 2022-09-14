define([
    'jquery',
    'underscore',
    'uiComponent',
    'Amasty_GiftCard/js/actions/create-settings-modal',
    'uiRegistry',
    'Amasty_GiftCard/js/model/draggable',
    'jquery/ui'
], function ($, _, Component, createModal, registry, model) {
    'use strict';

    return Component.extend({
        defaults: {
            custom_css: '',
            pos_y: null,
            pos_x: null,
            width: null,
            height: null,
            isDropValid: true,
            links: {
                custom_css: '${ $.provider }:${ $.dataScope }.custom_css',
                pos_y: '${ $.provider }:${ $.dataScope }.pos_y',
                pos_x: '${ $.provider }:${ $.dataScope }.pos_x',
                width: '${ $.provider }:${ $.dataScope }.width',
                height: '${ $.provider }:${ $.dataScope }.height'
            },
            listens: {
                pos_x: 'setPositionX',
                pos_y: 'setPositionY',
                width: 'setWidth',
                height: 'setHeight',
            },
            imports: {
                maxHeight: '${ $.provider }:data.height',
                maxWidth: '${ $.provider }:data.width'
            }
        },

        initObservable: function () {
            return this._super()
                .observe(['custom_css', 'pos_y', 'pos_x', 'height', 'width']);
        },

        openModal: function () {
            if (!this.modal) {
                return this.initializeModal()
            }

            this.modal.openModal();
        },

        afterRender: function (element) {
            var self = this;

            this.element = $(element);
            this.setPositionX(this.pos_x());
            this.setPositionY(this.pos_y());
            this.setHeight(this.height());
            this.setWidth(this.width());

            this.element.draggable({
                revert: function (isValid) {
                    if (!isValid) {
                        self.isDropValid = false;
                        return true;
                    }

                    return false;
                },
                revertDuration: 50,
                stop: function ( event, ui ) {
                    var width = self.element.outerWidth(),
                        height = self.element.outerHeight(),
                        position = {
                            pos_y: ui.position.top,
                            pos_x: ui.position.left
                        };

                    self.setCoordinates(position, width, height);
                }
            });
        },

        setCoordinates: function (position, width, height) {
            var max = {
                    pos_x: this.maxWidth - width,
                    pos_y: this.maxHeight - height
                },
                over = {
                    pos_x: position.pos_x > max.pos_x,
                    pos_y: position.pos_y > max.pos_y
                };

            this.setPosition(position, max, 'pos_x', over, 'left');
            this.setPosition(position, max, 'pos_y', over, 'top');
        },

        setPosition: function (position, max, key, over, cssKey) {
            var coordinate = Math.round(model.getPosition(over, position, max, key));

            this.element.css(cssKey, coordinate);
            this[key](coordinate);
        },

        setPositionX: function (pos_x) {
            if (this.element) {
                this.element.css('left', pos_x);
            }
        },

        setPositionY: function (pos_y) {
            if (this.element) {
                this.element.css('top', pos_y);
            }
        },

        setHeight: function (height) {
            if (this.element) {
                this.element.css('height', height);
                this.element.css('max-height', height);
            }
        },

        setWidth: function (width) {
            if (this.element) {
                this.element.css('width', width);
                this.element.css('max-width', width);
            }
        },

        initializeModal: function () {
            var parameters = {
                width: this.width(),
                height: this.height(),
                pos_y: this.pos_y(),
                pos_x: this.pos_x(),
                custom_css: this.custom_css()
            },
            modalName = createModal.call(this, this.code, this.label, false, parameters);

            registry.async(modalName)(function (modal) {
                this.modal = modal;
                this.modal.openModal();
            }.bind(this));
        }
    });
});
