define(
    [
        'jquery',
        'Amasty_PushNotifications/js/push_notification',
        'Magento_Customer/js/customer-data'
    ],
    function ($, PushNotification, customerData) {
        'use strict';

        return PushNotification.extend({
            defaults: {
                isEnableModule: false
            },

            /**
             *
             * @return {exports}
             */
            initialize: function () {
                this._super();
                this.initPromptEvents();
                var self = this;

                /** TODO new feature Expire notifications in days **/
                // this.setExpireDays();

                if (this.isEnableModule
                    && this.isNotificationsStatus(this.notificationStatusDefault)
                    && this.isTimeExpiredOrNotSet()
                ) {
                    window.setTimeout(function () {
                        self.showOrHidePrompt('show');
                    }, this.getDelayTimeInMilliseconds());
                }

                return this;
            },

            /**
             *
             * @return {exports}
             */
            sendTestNotification: function () {
                this.sendTest(this.urlAction, this.campaignId);

                return this;
            },

            /**
             *
             * @return {number}
             */
            getPromptFrequencyTimeExpired: function () {
                var timeExpired = 0;

                switch (this.promptFrequency) {
                    case 1:
                        timeExpired = 3600;
                        break;
                    case 2:
                        timeExpired = 86400;
                        break;
                    case 3:
                        timeExpired = 604800;
                        break;
                }

                return timeExpired;
            },

            /**
             *
             * @return {boolean}
             */
            isTimeExpiredOrNotSet: function () {
                var remindTime = customerData.get('amasty-pushn-notification-remind-time')(),
                    expireDays = customerData.get('amasty-pushn-notification-expire-days')(),
                    expireDays = customerData.get('amasty-pushn-notification-expire-days')(),
                    expireTimestamp = customerData.get('amasty-pushn-notification-expire-timestamp')();

                if (remindTime && remindTime > this.getCurrentTimestamp()) {
                    return false;
                }

                if (expireDays && expireTimestamp && (expireTimestamp < this.getCurrentTimestamp())) {
                    return false;
                }

                return true;
            },

            /**
             *
             * @return {number}
             */
            initPromptEvents: function () {
                var self = this;

                $('.am-defer').add('div.am-notification-wrapper > span.am-close').on('click', function () {
                    self.setRemindLater();
                    self.showOrHidePrompt('hide');
                });

                $('#am-submit').on('click', function () {
                    self.showOrHidePrompt('hide');
                    self.subscribeUser();
                });

                $('div.am-notification-error > span.am-close').on('click', function () {
                    $('.am-notification-error').hide();
                });
            },

            /**
             *
             * @return {exports}
             */
            subscribeUser: function () {
                this.subscribeUserAndSendToServer(this.subscribeActionUrl);

                return this;
            },

            /**
             *
             * @return {exports}
             */
            setRemindLater: function () {
                var promtFrequency = this.getPromptFrequencyTimeExpired(),
                    currentTimestamp = this.getCurrentTimestamp();

                customerData.set('amasty-pushn-notification-remind-time', currentTimestamp + promtFrequency);

                return this;
            },

            /**
             *
             * @return {exports}
             */
            setCountDailyShow: function () {
                var currentShowCount = customerData.get('amasty-pushn-notification-count-daily-show')();

                if (isNaN(currentShowCount)) {
                    currentShowCount = 0;
                }

                customerData.set('amasty-pushn-notification-count-daily-show', currentShowCount + 1);

                return this;
            },

            /**
             *
             * @return {exports}
             */
            setExpireDays: function () {
                var currentExpireDays = customerData.get('amasty-pushn-notification-expire-days')();

                if (isNaN(currentExpireDays)) {
                    this.setExpireDaysAndTimestamp(this.expireNotificationDay);
                } else {
                    if (currentExpireDays != this.expireNotificationDay) {
                        this.setExpireDaysAndTimestamp(this.expireNotificationDay);
                    }
                }

                return this;
            },

            /**
             *
             * @return {exports}
             */
            setExpireDaysAndTimestamp: function (expireNotificationDay) {
                customerData.set('amasty-pushn-notification-expire-days', expireNotificationDay);
                customerData.set(
                    'amasty-pushn-notification-expire-timestamp',
                    this.convertDaysInSeconds(this.expireNotificationDay) + this.getCurrentTimestamp()
                );

                return this;
            },


            /**
             *
             * @param days
             * @return {number}
             */
            convertDaysInSeconds: function (days) {
                return Math.floor(days * 86400);
            },

            /**
             *
             * @return {number}
             */
            getCurrentTimestamp: function () {
                return Math.floor(Date.now() / 1000);
            },

            /**
             *
             * @param action
             * @return {exports}
             */
            showOrHidePrompt: function (action) {
                if (action == 'show' && this.isCountDailyShowExpired()) {
                    $('.am-notification-wrapper').show();
                    this.setCountDailyShow();
                } else {
                    $('.am-notification-wrapper').hide();
                }

                return this;
            },

            /**
             *
             * @return {boolean}
             */
            isCountDailyShowExpired: function () {
                var showCount = parseInt(customerData.get('amasty-pushn-notification-count-daily-show')());

                if (this.maxNotificationsPerDay != 0 && showCount >= this.maxNotificationsPerDay) {
                    return false;
                }

                return true;
            },

            /**
             *
             * @return {number}
             */
            getDelayTimeInMilliseconds: function () {
                return this.promptDelay * 1000;
            }
        });
    }
);
