define([
    'jquery',
    'ko',
    'uiComponent',
    'Magento_Checkout/js/model/quote'
], function ($, ko, Component, quote) {
    'use strict';
    var checkoutConfig = window.checkoutConfig;
    return Component.extend({
        defaults: {
            template: 'Elogic_VendorShipping/checkout/shipping/carrier_custom'
        },

        initObservable: function () {

            this.selectedMethod = ko.computed(function() {
                var method = quote.shippingMethod();
                var selectedMethod = method != null ? method.carrier_code + '_' + method.method_code : null;
                var quoteData = quote.getItems();
                var quoteVendors = checkoutConfig.totalsData.vendors;

                return selectedMethod;
            }, this);

            return this;
        },

        getVendors: function() {
            var vendors = checkoutConfig.totalsData.vendors;
            return vendors;
        },

        getMediaUrl: function(methodCode) {
            var imageurl = window.checkoutConfig.mediaUrl;
            return imageurl;
        }
    });
});