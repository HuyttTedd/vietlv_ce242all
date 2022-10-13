define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    const $debugBlock = $('<div/>').addClass('mst-banner__debug');
    $('body').append($debugBlock);

    const $debugContainers = $('[data-debug-container]');
    _.each($debugContainers, function (debugContainer) {
        const $debugContainer = $(debugContainer);
        const $div = $('<div />').html($debugContainer.data('debug-container'));
        $debugBlock.append($div);

        $div.on('mousemove', function (e) {
            const $el = $(e.currentTarget);
            const data = $el.text();

            const $container = $('[data-debug-container="' + data + '"').parent();
            highlightContainer($container)
            //$debugContainers.parents().removeClass('mst-banner__container-debug');
            //
            //$('[data-debug-container="' + data + '"').parent().addClass('mst-banner__container-debug');
        });
    });

    function highlightContainer($el) {
        const w = $el.outerWidth();
        const h = $el.outerHeight();
        const t = $el.offset().top;
        const l = $el.offset().left;

        $('.mst-banner__container-highlight').remove();

        const $div = $('<div/>')
            .css('width', w)
            .css('height', h)
            .css('top', t)
            .css('left', l)
            .addClass('mst-banner__container-highlight');

        $('html, body').scrollTop(t);

        $('body').append($div);
    }
});
