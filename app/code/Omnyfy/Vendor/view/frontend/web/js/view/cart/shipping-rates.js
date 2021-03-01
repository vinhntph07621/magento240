/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'ko',
        'underscore',
        'uiComponent',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/select-shipping-method',
        'Omnyfy_Vendor/js/action/select-shipping-method-group',
        'Magento_Checkout/js/checkout-data'
    ],
    function (
        ko,
        _,
        Component,
        shippingService,
        priceUtils,
        quote,
        selectShippingMethodAction,
        selectShippingMethodGroupAction,
        checkoutData
    ) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Omnyfy_Vendor/cart/rates-template'
            },
            isVisible: ko.observable(!quote.isVirtual()),
            isLoading: shippingService.isLoading,
            shippingRates: shippingService.getShippingRates(),
            shippingRateGroups: ko.observableArray([]),
            selectedShippingMethod: ko.computed(function () {
                    var return_value = null;

                    if(!_.isEmpty(quote.shippingMethod()) && quote.shippingMethod()['extension_attributes'] !== undefined){
                        return_value = quote.shippingMethod()['carrier_code'] + '_' + quote.shippingMethod()['method_code'] + '_' + quote.shippingMethod()['extension_attributes']['vendor_id'] + '_' + quote.shippingMethod()['extension_attributes']['location_id'];
                    }

                    return return_value;
                }
            ),
            shippingNotesVisible: ko.observable(false),
            messageVisible: ko.observable(false),
            shippingNotes: ko.observable(),

            /**
             * @override
             */
            initObservable: function () {
                var self = this;
                this._super();
                var shippingNotesCount = 0;
                var shippingNotesValue = ''; 
                var allShippingCount = 0;
                //console.log("vendorName", self.vendorName, "vendorId",self.vendorId);
                //console.log("locationName", self.locationName, "locationId", self.locationId)
                this.shippingRates.subscribe(function (rates) {
                    self.shippingRateGroups([]);
                    // console.log(rates);

                    // var filteredRates = _.filter(rates, function (rate) {
                    //     return rate['extension_attributes']['vendor_id'] === self.vendorId && rate['extension_attributes']['location_id'] === self.locationId;
                    // });

                    // console.log(filteredRates);

                    _.each(rates, function (rate) {
/*                        var locationId = rate['extension_attributes']['location_id'];
                        var carrierTitle = rate['carrier_title'];


                        console.log(self.shippingRateGroups.indexOf(locationId));
                        if (self.shippingRateGroups.indexOf(locationId) === -1) {
                            self.shippingRateGroups.push(carrierTitle);
                        }*/

                        if(rate['carrier_code'] !== null){
                            allShippingCount++;
                        }else{
                            allShippingCount = 0;
                        }

                        if(rate['extension_attributes']['shipping_option_note'] && rate['extension_attributes']['shipping_option_note'] !== null && rate['method_code'] !== null){
                            shippingNotesValue = rate['extension_attributes']['shipping_option_note'];
                            shippingNotesCount++;
                        }else{
                            shippingNotesCount = 0;
                        }
                            
                        var carrierTitle = rate['carrier_title'];

                        if (self.shippingRateGroups.indexOf(carrierTitle) === -1) {
                            self.shippingRateGroups.push(carrierTitle);
                        }
                    });
                    
                    /*  exist all shipping notes */
                    if(allShippingCount > 0 ){
                        self.messageVisible(true);
                    }else{
                        self.messageVisible(false);
                    }

                    /*  exist sherpa shipping notes */
                    if(shippingNotesCount > 0 ){ 
                        self.shippingNotesVisible(true);
                        self.shippingNotes(shippingNotesValue);
                    }else{
                        self.shippingNotes(null);
                        self.shippingNotesVisible(false);
                    }
                    
                });

                return this;
            },

            /**
             * Get shipping rates for specific group based on title.
             * @returns Array
             */
            getRatesForGroup: function (shippingRateGroupTitle) {

                var self = this;
                // console.log(this.shippingRates());
                var filteredRates = _.filter(this.shippingRates(), function (rate) {
                    return rate['extension_attributes']['vendor_id'] === self.vendorId && rate['extension_attributes']['location_id'] === self.locationId;
                });
                // console.log(filteredRates);
                var result = _.filter(filteredRates, function (rate) {
                    return shippingRateGroupTitle === rate['carrier_title'];
                });
                // console.log(result);
                return result;
            },

            /**
             * Format shipping price.
             * @returns {String}
             */
            getFormattedPrice: function (price) {
                return priceUtils.formatPrice(price, quote.getPriceFormat());
            },

            /**
             * Set shipping method.
             * @param {String} methodData
             * @returns bool
             */
            selectShippingMethod: function (methodData) {
                
                var locationId = methodData['extension_attributes']['location_id'];

                selectShippingMethodGroupAction(methodData, locationId);
                checkoutData.setSelectedShippingRate(methodData);
                checkoutData.setSelectedShippingMethodGroup(quote.shippingMethodGroup());

                return true;
            }
        });
    }
);
