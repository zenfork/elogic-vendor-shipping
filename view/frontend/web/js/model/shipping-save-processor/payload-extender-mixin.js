/*
/!*jshint browser:true jquery:true*!/
/!*global alert*!/
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/quote'
], function ($, wrapper, quote) {
    'use strict';

    return function (setShippingInformationAction) {

        return wrapper.wrap(setShippingInformationAction, function (originalAction) {
            var shippingAddress = quote.shippingAddress();
            var shippingVendorValue = $('[name="select_vendor_shipping"]') ? $('[name="select_vendor_shipping"]').val() : '';

            if (shippingAddress['extension_attributes'] === undefined) {
                shippingAddress['extension_attributes'] = {
                    'quote_vendor_shipping': {
                        'vendor_id': shippingVendorValue
                    }
                }
            }

            return originalAction();
        });
    };
});
*/

;define([
    'jquery',
    'mage/utils/wrapper',
    'underscore'
], function ($, wrapper, _) {
    'use strict';

    return function (payloadExtender) {
        return wrapper.wrap(payloadExtender, function (originalFunction, payload) {
            var shippingVendorValue = $('[name="select_vendor_shipping"]') ? $('[name="select_vendor_shipping"]').val() : '';

            payload = originalFunction(payload);

            _.extend(payload.addressInformation, {
                extension_attributes: {
                    'vendor_shipping': {
                        'vendor_id': shippingVendorValue
                    }
                }
            });

            return payload;
        });
    };
});
