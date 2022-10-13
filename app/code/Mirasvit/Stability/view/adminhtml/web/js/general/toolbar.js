define([
    'jquery',
    'underscore',
    'Magento_Ui/js/lib/spinner',
    'uiComponent'
], function ($, _, spinner, Component) {
    'use strict';
    
    return Component.extend({
        initialize: function () {
            this._super();
            
            this.hideLoader();
            
            return this;
        },
        
        hideLoader: function () {
            $('[data-component]').hide();
            //var name = this.name.replace(".toolbar", '');
            //spinner.get(name).hide();
        }
    });
});
