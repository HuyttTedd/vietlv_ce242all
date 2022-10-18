/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko'
], function (ko) {
    'use strict';

    var extraFeeData = ko.observableArray([]);

    return {
        isLoading: ko.observable(false),

        /**
         * Set fees
         *
         * @param {*} data
         */
        setExtraFeeData: function (data) {
            extraFeeData(data);
        },

        /**
         * Get shipping rates
         *
         * @returns {*}
         */
        getExtraFeeData: function () {
            return extraFeeData;
        }
    };
});
