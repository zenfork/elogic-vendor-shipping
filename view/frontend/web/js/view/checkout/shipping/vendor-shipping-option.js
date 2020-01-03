;define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote'
], function ($, ko, Component, quote) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Elogic_VendorShipping/checkout/shipping/vendor-shipping-option'
        },

        initObservable: function () {
            // returns our original component which we extend
            var self = this._super();

            this.selectedMethod = ko.computed(function() {
                var method = quote.shippingMethod();
                var selectedMethod = method != null ? method.carrier_code + '_' + method.method_code : null;
                // var quoteData = quote.getItems();
                // var quoteVendors = checkoutConfig.vendors;

                return selectedMethod;
            }, this);

            this.showAdditionalOption = ko.computed(function() {
                var method = quote.shippingMethod();

                if (method && method['carrier_code'] !== undefined) {
                    return true;
                }

                return false;

            }, this);

            return this;
        },

        getVendors: function() {
            var vendors = checkoutConfig.vendors;
            return vendors;
        },

        getMediaUrl: function(methodCode) {
            var imageurl = window.checkoutConfig.mediaUrl;
            return imageurl;
        }
    });
});