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
    'Magento_Checkout/js/model/resource-url-manager',
    'Magento_Checkout/js/model/quote',
    'mage/url'
], function ($, resourceUrlManager, quote, url) {
    'use strict';

    return $.extend(resourceUrlManager, {
        getUploadUrl: function () {
            var params = resourceUrlManager.getCheckoutMethod() === 'guest' ? {cartId: quote.getQuoteId()} : {},
                urls = {
                    'guest': '/guest-carts/:cartId/mp-order-attributes/upload',
                    'customer': '/carts/mine/mp-order-attributes/upload'
                };

            return url.build(resourceUrlManager.getUrl(urls, params));
        },
    });
});
