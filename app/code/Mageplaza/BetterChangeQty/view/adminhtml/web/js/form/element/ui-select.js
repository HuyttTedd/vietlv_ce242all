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
 * @package     Mageplaza_BetterChangeQty
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'underscore',
], function ($, _) {
    'use strict';

    return function (toggleListVisible) {
        return toggleListVisible.extend({
            toggleListVisible: function () {
                var categoryDefault = $('#mpbetterchangeqty_general_apply_category_inherit');

                if (categoryDefault.length && categoryDefault.is(':checked')) {
                    return this;
                } else {
                    this.listVisible(!this.disabled() && !this.listVisible());
                }

                return this;
            }
        })
    }
});

