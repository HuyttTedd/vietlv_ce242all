/* global firebase, navigator */
define(
    [
        'jquery',
        'uiComponent',
        'mage/storage',
        'ko',
        'Magento_Ui/js/modal/alert',
        'mage/translate',
        'Amasty_PushNotifications/js/firebase'
    ],
    function ($, Component, storage, ko, alert, $t) {
        'use strict';

        return Component.extend({
            messaging: false,
            senderId: '',
            notificationStatusGranted: 'granted',
            notificationStatusDenied: 'denied',
            notificationStatusDefault: 'default',
            alertModalStatusError: $t('Error'),
            alertModalStatusSuccess: $t('Success'),
            alertModalStatusFail: $t('Fail'),

            /**
             *
             * @param senderId
             * @return {exports}
             */
            initialize: function (senderId) {

                if (senderId) {
                    this.setSenderId(senderId);
                }

                this._super();

                if (!this.getSenderId()) {
                    return;
                }

                firebase.initializeApp({
                    messagingSenderId: this.getSenderId()
                });

                var self = this;

                // if ('Notification' in window) {
                    this.messaging = firebase.messaging();

                    this.messaging.onMessage(function (payload) {
                        navigator.serviceWorker.getRegistration(window.firebaseUrl + 'firebase-cloud-messaging-push-scope')
                            .then(function (registration) {
                                if (registration !== undefined) {
                                    payload.notification['data'] = payload.notification;
                                    registration.showNotification(payload.notification.title, payload.notification);
                                }
                            });
                    });
                // }

                return this;
            },

            /**
             *
             * @param obj
             * @param src
             * @return {*}
             */
            extendObject: function (obj, src) {
                for (var key in src) {
                    if (src.hasOwnProperty(key)) {
                        obj[key] = src[key];
                    }
                }

                return obj;
            },

            /**
             *
             * @param actionUrl
             * @param ajaxParams
             */
            subscribe: function (actionUrl, ajaxParams) {
                var self = this;
                this.messaging.requestPermission()
                    .then(function () {
                        self.messaging.getToken()
                            .then(function (currentToken) {
                                if (currentToken) {
                                    ajaxParams.userToken = currentToken;
                                    self.sendTokenToServer(currentToken, actionUrl, ajaxParams);
                                }
                            })
                            .catch(function (err) {
                                self.showAlertModal(self.alertModalStatusError, $t('Token getting error.'));
                            });
                    })
                    .catch(function (err) {
                        if (Notification.permission === 'denied') {
                            $('.am-notification-error').show();
                        } else {
                            self.showAlertModal(
                                self.alertModalStatusError,
                                $t('Not possible getting Request permissions.')
                            );
                        }
                    });
            },

            /**
             *
             * @param actionUrl
             * @param campaignId
             */
            sendTest: function (actionUrl, campaignId) {
                var self = this,
                    ajaxParams = {
                        campaignId: campaignId
                    };

                if (!this.isNotificationsPermitted()) {
                    this.subscribe(actionUrl, ajaxParams);
                }

                this.messaging.getToken()
                    .then(function (currentToken) {
                        if (currentToken) {
                            ajaxParams.userToken = currentToken;
                            self.sendTokenToServer(currentToken, actionUrl, ajaxParams);
                        }
                    })
                    .catch(function (err) {
                        self.showAlertModal(self.alertModalStatusError, $t('Token getting error.'));
                    });
            },

            /**
             *
             * @param actionUrl
             * @return {exports}
             */
            subscribeUserAndSendToServer: function (actionUrl) {
                var self = this,
                    ajaxParams = {};

                if (!this.isNotificationsPermitted()) {
                    this.subscribe(actionUrl, ajaxParams);
                }

                this.messaging.getToken()
                    .then(function (currentToken) {
                        if (currentToken) {
                            ajaxParams.userToken = currentToken;
                            self.sendTokenToServer(currentToken, actionUrl, ajaxParams);
                        }
                    })
                    .catch(function (err) {
                        self.showAlertModal(self.alertModalStatusError, $t('Token getting error.'));
                    });

                return this;
            },

            /**
             *
             * @param currentToken
             * @param actionUrl
             * @param ajaxParams
             * @return {exports}
             */
            sendTokenToServer: function (currentToken, actionUrl, ajaxParams) {
                var self = this,
                    contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
                this.ajaxLoader('show');

                storage.post(actionUrl, ajaxParams, false, contentType).done(function (result) {
                    self.ajaxLoader('hide');
                    var titleModal = result.status ? self.alertModalStatusSuccess : self.alertModalStatusError;
                    self.showAlertModal(titleModal, result.message);
                }).fail(function (response) {
                    self.ajaxLoader('hide');
                    self.showAlertModal(self.alertModalStatusFail, response.message);
                });

                return this;
            },

            /**
             *
             * @param title
             * @param content
             */
            showAlertModal: function (title, content) {
                alert({
                    title: title,
                    content: content
                });
            },

            /**
             *
             * @return {boolean}
             */
            isNotificationsPermitted: function () {
                return true;
                if ('Notification' in window && Notification.permission === 'granted') {
                    return true;
                }

                return false;
            },

            /**
             *
             * @return {boolean}
             */
            isNotificationsStatus: function (status) {
                if ('Notification' in window && Notification.permission === status) {
                    return true;
                }

                return false;
            },

            /**
             *
             * @return {boolean}
             */
            ajaxLoader: function (action) {
                $('body').loader(action);

                return true;
            },

            /**
             *
             * @param senderId
             * @return {exports}
             */
            setSenderId: function (senderId) {
                this.senderId = senderId;

                return this;
            },

            /**
             *
             * @return {string}
             */
            getSenderId: function () {
                return this.senderId;
            }
        });
    }
);
