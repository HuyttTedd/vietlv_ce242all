define([
    'jquery',
    'underscore',
    'uiComponent'
], function ($, _, Component) {
    'use strict';
    return Component.extend({
        defaults: {
            url: ""
        },
        
        initialize: function () {
            this._super();
            
            if (window.jsErrors) {
                _.each(window.jsErrors, function (error) {
                    this.sendError(error);
                }.bind(this));
            }
            
            window.onerror = function (error) {
                this.sendError(error);
            }.bind(this);
        },
        
        
        sendError: function (error) {
            $.ajax(this.url, {
                method: 'get',
                data:   {
                    uri:   window.location.href,
                    error: error
                }
            });
        }
    })
});