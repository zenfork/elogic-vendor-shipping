;define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/element/select',
    'Magento_Checkout/js/model/quote'
], function ($, ko, select, quote) {
    'use strict';

    var self;
    var checkoutConfig = window.checkoutConfig;

    return select.extend({

        initialize: function () {
            self = this;
            this._super();
            this.selectedShippingMethod = quote.shippingMethod();

            quote.shippingMethod.subscribe(function(){

                var method = quote.shippingMethod();

                if(method && method['carrier_code'] !== undefined) {
                    if(!self.selectedShippingMethod || (self.selectedShippingMethod
                        && self.selectedShippingMethod['carrier_code'] != method['carrier_code'])) {
                            self.selectedShippingMethod = method;
                            self.updateDropdownValues(method);
                    }
                }

            }, null, 'change');
        },

        /**
         * Called when shipping method is changed.
         * Also called when initial selection is made.
         *
         * @param value
         * @returns {Object} Chainable
         */
        updateDropdownValues: function(method) {
            var quoteVendors = checkoutConfig.vendors;
            var vendorsCollection = [];
            // var valuesCollection = [];

            quoteVendors.forEach (function(vendor) {
                vendorsCollection.push({
                    label: vendor.name,
                    value: vendor.id
                })
            });

            // if(method['carrier_code'] == 'freeshipping'){
            //     valuesCollection = [
            //         {
            //             label: 'Monday',
            //             value: 'Monday'
            //         },
            //         {
            //             label: 'Wednesday',
            //             value: 'Wednesday'
            //         },
            //         {
            //             label: 'Friday',
            //             value: 'Friday'
            //         }
            //     ];
            // } else {
            //     valuesCollection = [
            //         {
            //             label: 'Today',
            //             value: 'Today'
            //         },
            //         {
            //             label: 'Tomorrow',
            //             value: 'Tomorrow'
            //         }
            //     ];
            // }

            // console.log(quoteVendors);
            // console.log(vendorsCollection);
            // console.log(valuesCollection);

            self.updateDropdown(vendorsCollection);
        },

        /**
         * Called when option is changed in store selection list.
         * Also called when initial selection is made.
         *
         * @param value
         * @returns {Object} Chainable
         */
        updateDropdown: function(value) {
            this.setOptions(value);
        }
    });
});