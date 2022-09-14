define([
    'underscore',
    'mageUtils',
    'uiLayout'
], function (_, utils, layout) {
    'use strict';

    return function (data, positions) {
        var options = {
                height: data.height || data.default.height,
                width: data.width || data.default.width,
                custom_css: data.custom_css || data.default.custom_css,
                value: data.value,
                code: data.name,
                label: data.label
            },
            pos_y = _.isUndefined(positions) ? data.pos_y : positions.pos_y,
            pos_x = _.isUndefined(positions) ? data.pos_x : positions.pos_x;

        var field = utils.extend(options, {
            name: this.name + '.' + data.name,
            component: 'Amasty_GiftCard/js/form/element/draggable-element',
            provider: this.provider,
            dataScope: this.dataScope + '.image_elements.' + data.name,
            isDraggableElem: true,
            pos_y: pos_y,
            pos_x: pos_x,
            template: 'Amasty_GiftCard/draggable/draggable-element',
        });

        layout([field]);
        this.insertChild(field.name);
    };
});
