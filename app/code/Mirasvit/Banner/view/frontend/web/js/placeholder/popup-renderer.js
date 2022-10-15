define([
    'jquery',
    'jquery/jquery.cookie'
], function ($) {
    'use strict';

    $.widget('mst.popupRenderer', {
        options: {
            'placeholder_id': null
        },

        _create: function () {
            const $el = this.element;

            if (!this.canShow()) {
                return;
            }

            setTimeout(function () {
                $el.addClass('_active');

                $.cookie(this.getCookieName(), "+", {expires: 1, path: '/'});
            }.bind(this), 3000);

            $('[data-element=close]', $el).on('click', function () {
                $el.remove();
            }.bind(this))
        },

        canShow: function () {
            return $.cookie(this.getCookieName()) !== "+";
        },

        getCookieName: function () {
            return 'mstBanner_placeholder_' + this.options.placeholder_id;
        }
    });

    return $.mst.popupRenderer;
});
