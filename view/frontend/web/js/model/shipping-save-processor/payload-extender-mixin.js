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

            if (shippingVendorValue !== '') {
                _.extend(payload.addressInformation, {
                    extension_attributes: {
                        'vendor_shipping': {
                            'vendor_id': shippingVendorValue
                        }
                    }
                });
            }

            return payload;
        });
    };
});
