//
define(
    ['ko'],
    function (ko) {
        'use strict';

        var locationData = window.checkoutConfig.locationData;
        var locations = ko.observable(locationData);
        var shippingConfiguration = window.checkoutConfig.shippingConfiguration;
        var overallShippingIds = window.checkoutConfig.shippingOverallId;
        return {
            locations: locations,
            getLocations: function() {
                var result = [];

                for(var id in locationData ) {
                    result.push(locationData[id]);
                }
                return result;
            },
            getLocationById: function(locationId) {
                for(var id in locationData) {
                    if (id == locationId) {
                        return locationData[id];
                    }
                }
                return false;
            }
        };
    }
);