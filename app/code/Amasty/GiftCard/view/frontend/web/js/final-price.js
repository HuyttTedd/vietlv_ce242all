/**
 * Gift card  final price
 */

define([
    'uiComponent'
], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            isLoaded: false,
            showCustomPrice: false,
            priceComponentName: '',
            imports: {
                'showCustomPrice': '${ $.priceComponentName }:showCustomPrice'
            }
        },

        initialize: function () {
            this._super();

            this.isLoaded(true);

            return this;
        },

        initObservable: function () {
            this._super().observe([
                'showCustomPrice',
                'isLoaded'
            ]);

            return this;
        }
    });
});
