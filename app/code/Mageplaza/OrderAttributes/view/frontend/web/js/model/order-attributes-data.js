/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_OrderAttributes
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'Magento_Customer/js/customer-data'
], function ($, storage) {
    'use strict';

    var cacheKey = 'order-attributes-data';

    var getData = function () {
        var data = storage.get(cacheKey)();

        if ($.isEmptyObject(data)) {
            data = {
                'mpShippingAddressAttributes': {},
                'mpPaymentMethodTopAttributes': {}
            };
            saveData(data);
        }

        return data;
    };

    var saveData = function (data) {
        storage.set(cacheKey, data);
    };

    return {
        setData: function (key, data) {
            var obj = getData();
            obj[key] = data;
            saveData(obj);
        },

        getData: function (key) {
            if (typeof key === 'undefined') {
                return getData();
            }

            return getData()[key];
        }
    }
});
