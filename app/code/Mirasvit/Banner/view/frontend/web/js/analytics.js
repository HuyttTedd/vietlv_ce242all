define([
    'jquery',
    'underscore'
], function ($, _) {
    'use strict';

    var options = {
        url:    '',
        params: {
            uri: ''
        }
    };

    var pushed = {};

    var analytics = {
        init: function () {
            setInterval(function () {
                _.each($('[data-banner]'), function (banner) {
                    var $banner = $(banner);
                    var id = $banner.attr('data-banner');

                    if (this.isVisible($banner)) {
                        this.push(id);
                    }
                }.bind(this));
            }.bind(this), 1000);
        },

        isVisible: function ($el) {
            if (!$el.is(":visible")) {
                return false;
            }

            const $win = $(window);

            const elementTop = $el.offset().top;
            const elementBottom = elementTop + $el.outerHeight();

            const viewportTop = $win.scrollTop();
            const viewportBottom = viewportTop + $win.height();

            return elementBottom > viewportTop && elementTop < viewportBottom;
        },

        push: function (bannerID) {
            if (pushed[bannerID] === true) {
                return;
            }

            pushed[bannerID] = true;

            $.post(options.url, {
                'banner_id': bannerID,
                'action':    'impression',
                'referrer':  options.params['uri']
            });
        },

        'Mirasvit_Banner/js/analytics': function (settings) {
            options = _.extend(options, settings);

            analytics.init();
        }
    };

    return analytics;
});
