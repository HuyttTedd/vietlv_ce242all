define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    return  {
        options: {
            css: {
                used: '-used',
                draggable: '.ui-draggable-dragging',
                hovered: '-hovered'
            }
        },

        /**
         * Initialize DropZone
         *
         * @param {object} context
         * @param {object} element
         * @return {void}
         */
        initDroppable: function (context, element) {
            var self = this;

            $(element).droppable({
                over: function (event) {
                    $(event.target).parent().addClass(self.options.css.hovered);
                },
                out: function (event) {
                    $(event.target).parent().removeClass(self.options.css.hovered);
                },
                drop: function (event, ui) {
                    $(event.target).parent().removeClass(self.options.css.hovered);
                    context.onDropEnd.call(context, event, ui);
                }
            });
        },

        /**
         * Calculate position for dragged element
         *
         * @param {object} event
         * @param {object} max
         * @return {object}
         */
        calculatePosition: function (event, max) {
            var $element = $(this.options.css.draggable),
                imagePosition = $(event.target).offset(),
                elLeft = parseInt($element.css('left')),
                elTop = parseInt($element.css('top')),
                position = {
                    pos_y: elTop - parseInt(imagePosition.top),
                    pos_x: elLeft - parseInt(imagePosition.left)
                },
                over = {
                    pos_x: position.pos_x > max.pos_x,
                    pos_y: position.pos_y > max.pos_y
                };

            return {
                pos_y: this.getPosition(over, position, max, 'pos_y'),
                pos_x: this.getPosition(over, position, max, 'pos_x')
            };
        },

        getPosition: function (over, position, max, key) {
            if (!over[key] && (position[key] >= 0)) {
                return position[key];
            } else if (position[key] < 0) {
                return 0;
            } else if (over[key]) {
                return max[key];
            }
        },

        restoreElement: function (code) {
            $('[data-code="' + code + '"]').removeClass(this.options.css.used);
        },

        cloneData: function (data) {
            if (!data) {
                return;
            }

            var clonedData = {};

            this.deepClone(data, clonedData);

            return clonedData;
        },

        /**
         * Recursively copies an object
         *
         * @param {object} data
         * @param {object} object
         * @return {void}
         */
        deepClone: function (data, object) {
            Object.keys(data).forEach(function (key) {
                if (_.isObject(data[key])) {
                    object[key] = {};
                    this.deepClone(data[key], object[key]);

                    return;
                }

                object[key] = data[key];
            }.bind(this));
        },

        getRemovedCodes: function (config) {
            return _.filter(Object.keys(config), function (key) {
                var elems = _.filter(this.elems(), function (elem) {
                    return elem.code === key;
                });
                return !elems.length;
            }.bind(this));
        },

        filterData: function (data, defaultElems) {
            return _.filter(defaultElems, function (elem) {
                return _.contains(Object.keys(data), elem.name);
            });
        }
    };
});
