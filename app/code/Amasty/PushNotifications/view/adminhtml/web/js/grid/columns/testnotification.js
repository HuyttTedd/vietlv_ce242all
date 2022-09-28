define([
    'Magento_Ui/js/grid/columns/column',
    'jquery',
    'Amasty_PushNotifications/js/push_notification'
], function (Column, $, PushNotification) {
    'use strict';

    return Column.extend({
        pushObject: false,
        defaults: {
            bodyTmpl: 'ui/grid/cells/html',
            fieldClass: {
                'data-grid-html-cell': true
            }
        },

        /**
         *
         * @param row
         * @return {*}
         */
        getLabel: function (row) {
            return row[this.index + '_html']
        },

        /**
         *
         * @param row
         * @return {exports}
         */
        sendTest: function (row) {
            var senderId = row[this.index + '_senderId'],
                urlAction = row[this.index + '_urlAction'],
                campaignId = row['campaign_id'],
                pushObject = this.initPushNotificationObject(senderId);

            pushObject.sendTest(urlAction, campaignId);

            return this;
        },

        /**
         *
         * @param row
         * @return {(function(this:exports))|Function}
         */
        getFieldHandler: function (row) {
            return this.sendTest.bind(this, row);
        },

        /**
         *
         * @param senderId
         * @return {boolean}
         */
        initPushNotificationObject: function (senderId) {
            if (this.pushObject === false) {
                this.pushObject = new PushNotification(senderId);
            }

            return this.pushObject;
        }
    });
});