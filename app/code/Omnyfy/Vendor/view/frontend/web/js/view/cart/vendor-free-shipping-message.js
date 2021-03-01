define([
    'jquery',
    'underscore',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/quote',
    'domReady!'
], function ($, _, priceUtils, quote) {
    'use strict';

    return function(config, element) {

        var locationId,
            locationItems,
            locationTotal = 0,
            message = '';

        locationId = $(element).data('location');
        locationItems = _.filter(quote.getItems(), function(_item) {
            return _item.location_id == locationId;
        });

        _.each(locationItems, function(_item) {
            locationTotal = parseFloat(locationTotal) + parseFloat(_item.row_total);
        });

        if (locationTotal < parseFloat(config.threshold)) {
            var amountRemaining = priceUtils.formatPrice(parseFloat(config.threshold) - locationTotal, quote.getPriceFormat())
            message = config.messageUnderThreshold.replace('[amount remaining]', amountRemaining).replace('[vendor name]', $(element).data('vendor'));
        } else {
            message = config.messageThresholdReached.replace('[vendor name]', $(element).data('vendor'));
        }

        $(element).html(message);
    }
})