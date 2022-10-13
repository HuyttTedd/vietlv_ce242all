define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    var options = {
        url:    '',
        params: {}
    };

    var loader = {
        init: function () {
            var placeholderIds = [];

            _.each($('[data-banner-placeholder]'), function (placeholder) {
                const id = $(placeholder).data('banner-placeholder');
                const isLoaded = $(placeholder).data('loaded');

                if (!isLoaded) {
                    placeholderIds.push(id);
                }
            });

            if (placeholderIds.length === 0) {
                return;
            }

            $.getJSON(
                options.url,
                _.extend(options.params, {
                    placeholder_id: placeholderIds
                })
            ).done(function (response) {
                _.each(response['placeholders'], function (item) {
                    this.setPlaceholder(item['placeholder_id'], item['html']);
                }.bind(this));
            }.bind(this)).fail(function (error) {
                console.log(error);
            })
        },

        setPlaceholder: function (placeholderId, html) {
            const $placeholder = $('[data-banner-placeholder="' + placeholderId + '"]');
            if ($placeholder.length) {
                $placeholder.html(html);

                $placeholder.trigger('contentUpdated');
            }
        },

        'Mirasvit_Banner/js/loader': function (settings) {
            options = _.extend(options, settings);

            loader.init();
        }
    };


    return loader;
});
