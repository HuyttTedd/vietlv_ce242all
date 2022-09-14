define([
    'jquery',
    'underscore',
    'uiComponent',
    'mageUtils',
    'Amasty_GiftCard/js/actions/create-settings-modal',
    'uiRegistry',
    'Amasty_GiftCard/js/actions/create-draggable-element',
    'Amasty_GiftCard/js/model/draggable',
    'Amasty_GiftCard/js/actions/delete-image',
    'jquery/ui',
], function (
    $,
    _,
    Component,
    utils,
    createModal,
    registry,
    createElem,
    model,
    deleteImage
) {
    'use strict';

    return Component.extend({
        defaults: {
            files: [],
            listens: {
                files: 'expandImage',
                image: 'disableAddImageMode',
                elems: 'checkElems',
                imageWidth: 'setImageWidth',
                imageHeight: 'setImageHeight'
            },
            defaultData: [],
            modules: {
                fileUploader: '${ $.parentName }.image'
            },
            css: {
                used: '-used'
            },
            savedElements: {}
        },

        initialize: function () {
            this._super();

            this.initDndElements();

            return this;
        },

        initObservable: function () {
            return this._super()
                .observe({
                    image: null,
                    visible: false,
                    isDesignMode: false,
                    isDeleteCardVisible: false,
                    imageWidth: 580,
                    imageHeight: 390,
                    addImageMode: false,
                    currentConfig: {},
                    isShowEditButton: true
                });
        },

        setImageHeight: function (height) {
            if (this.element) {
                this.element.css('height', height)
            }
        },

        setImageWidth: function (width) {
            if (this.element) {
                this.element.css('width', width)
            }
        },

        initDndElements: function () {
            var data = model.filterData(this.savedElements, this.defaultData);

            data.forEach(function (item) {
                createElem.call(this, item);
            }.bind(this));
        },

        afterRender: function (element) {
            this.element = $(element);
            this.setImageHeight(this.imageHeight());
            this.setImageWidth(this.imageWidth());
            model.initDroppable(this, element);
        },

        checkElems: function (elems) {
            var hasDragElems = elems.some(function (elem) {
                return !!elem.code;
            });

            this.isShowEditButton(!hasDragElems);
        },

        expandImage: function (value) {
            if (_.isArray(value)) {
                this.image(value[0]);
                this.visible(!!value.length);
            }
        },

        disableAddImageMode: function (value) {
            if (value) {
                this.addImageMode(false);
            }
        },

        toggleDesignMode: function () {
            this.isDesignMode(!this.isDesignMode());
        },

        /**
         * Drop End
         *
         * @param {object} event
         * @param {object} ui
         * @return {void/boolean}
         */
        onDropEnd: function (event, ui) {
            if (ui.draggable.data('amgcardElement')) {
                return false;
            }

            var options = ui.draggable.data('item'),
                max = {
                    pos_x: this.imageWidth() - options.default.width,
                    pos_y: this.imageHeight() - options.default.height
                },
                positions = model.calculatePosition(event, max);

            ui.draggable.addClass(this.css.used);
            createElem.call(this, options, positions);
        },

        openModal: function () {
            if (!this.modal) {
                return this.initializeModal()
            }

            this.modal.openModal();
        },

        initializeModal: function () {
            var size = {
                    width: this.imageWidth,
                    height: this.imageHeight
                },
                modalName = createModal.call(this, 'image', 'Image', true, size);

            registry.async(modalName)(function (modal) {
                this.modal = modal;
                this.modal.openModal();
            }.bind(this));
        },

        /**
         * Remove child element
         *
         * @param {string} code
         * @return {void}
         */
        removeElement: function (code) {
            var name = this.name + '.' + code,
                child = this.getChild(name);

            this.removeChild(name);
            this.source.remove(this.dataScope + '.image_elements.' + code);
            child.destroy(true);

            model.restoreElement(code);
        },

        /**
         * Remove all child elements
         *
         * @return {void}
         */
        removeAllChildren: function () {
            this.elems.each(function (elem) {
                if (elem.code) {
                    this.source.remove(elem.dataScope);
                    elem.destroy(true);
                }
            }.bind(this));

            this._updateCollection();
        },

        deleteImage: function () {
            deleteImage.call(this, true);
        },

        useChangeMode: function () {
            this.toggleDesignMode();
            this.currentConfig(model.cloneData(this.source.data.image_elements));
        },

        cancel: function () {
            this.toggleDesignMode();
            this.restoreChanges();
        },

        apply: function () {
            this.toggleDesignMode();
            this.currentConfig('');
        },

        /**
         * Restore changes after cancel
         *
         * @return {void}
         */
        restoreChanges: function () {
            var config = this.currentConfig(),
                removedElementCodes;

            if (!config) {
                return;
            }

            removedElementCodes = model.getRemovedCodes.call(this, config);
            if (removedElementCodes.length) {
                this.restoreElements(removedElementCodes, config);
            }

            this.elems.each(function (elem) {
                if (elem.code) {
                    this.removeOrRestore(config, elem);
                }
            }.bind(this));
        },

        /**
         * Create restored elements
         *
         * @param {array} codes
         * @param {object} config
         * @return {void}
         */
        restoreElements: function (codes, config) {
            codes.forEach(function (code) {
                var options = config[code],
                    data = this.defaultData.find(function (elem) {
                        return elem.name === code;
                    });

                data = utils.extend({}, data, options);
                createElem.call(this, data);
            }.bind(this));
        },

        removeOrRestore: function (config, elem) {
            var elemOptions = config[elem.code];

            if (!elemOptions) {
                this.removeElement(elem.code);
                model.restoreElement(elem.code);

                return;
            }

            Object.keys(elemOptions).forEach(function (key) {
                elem[key](elemOptions[key]);
            });
        }
    });
});
