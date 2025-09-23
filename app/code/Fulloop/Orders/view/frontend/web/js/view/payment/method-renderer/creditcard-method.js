define([
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/action/redirect-on-success',
    'mage/url'
], function (Component, redirectOnSuccessAction, url) {
    'use strict';
        return Component.extend({
            defaults: {
                template: 'Fulloop_Orders/payment/creditcard'
            },
            getMailingAddress: function () {
                return window.checkoutConfig.payment.checkmo.mailingAddress;
            },
            getInstructions: function () {
                return window.checkoutConfig.payment.instructions[this.item.method];
            },
            afterPlaceOrder: function () {
                redirectOnSuccessAction.redirectUrl = url.build('fulloop_orders/payment/creditcard/');
                this.redirectAfterPlaceOrder = true;
            },
        });
    }
);
