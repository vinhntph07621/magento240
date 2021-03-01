/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    ['ko'],
    function (ko) {
        'use strict';
        var billingAddress = ko.observable(null);
        var shippingAddress = ko.observable(null);
        //var shippingMethod = ko.observable(null);
        var shippingMethod = ko.observable({});
        var paymentMethod = ko.observable(null);
        var quoteData = window.checkoutConfig.quoteData;
        var basePriceFormat = window.checkoutConfig.basePriceFormat;
        var priceFormat = window.checkoutConfig.priceFormat;
        var storeCode = window.checkoutConfig.storeCode;
        var totalsData = window.checkoutConfig.totalsData;
        var totals = ko.observable(totalsData);
        var collectedTotals = ko.observable({});
        var shippingConfiguration = window.checkoutConfig.shippingConfiguration;
        var shippingOverallId = window.checkoutConfig.shippingOverallId;
        return {
            totals: totals,
            shippingAddress: shippingAddress,
            shippingMethod: shippingMethod,
            billingAddress: billingAddress,
            paymentMethod: paymentMethod,
            guestEmail: null,
            shippingMethodGroup: ko.observable({}),
            getQuoteId: function() {
                return quoteData.entity_id;
            },
            isVirtual: function() {
                return !!Number(quoteData.is_virtual);
            },
            getPriceFormat: function() {
                return priceFormat;
            },
            getBasePriceFormat: function() {
                return basePriceFormat;
            },
            getItems: function() {
                return window.checkoutConfig.quoteItemData;
            },
            getTotals: function() {
                return totals;
            },
            setTotals: function(totalsData) {
                if (_.isObject(totalsData.extension_attributes)) {
                    _.each(totalsData.extension_attributes, function(element, index) {
                        totalsData[index] = element;
                    });
                }
                totals(totalsData);
                this.setCollectedTotals('subtotal_with_discount', parseFloat(totalsData.subtotal_with_discount));
            },
            setPaymentMethod: function(paymentMethodCode) {
                paymentMethod(paymentMethodCode);
            },
            getPaymentMethod: function() {
                return paymentMethod;
            },
            getStoreCode: function() {
                return storeCode;
            },
            setCollectedTotals: function(code, value) {
                var totals = collectedTotals();
                totals[code] = value;
                collectedTotals(totals);
            },
            getCalculatedTotal: function() {
                var total = 0.;
                _.each(collectedTotals(), function(value) {
                    total += value;
                });
                return total;
            },
            getLocationIds: function(){
                var result = [];
                _.each(this.getItems(), function(item){
                    if (!isNaN(item.booking_id) && item.booking_id>0) {
                        return;
                    }
                    if (shippingConfiguration == 'overall_cart' && shippingOverallId) {
                        result.push(shippingOverallId);
                    }
                    else if (! (_.contains(result, item.location_id))) {
                        result.push(item.location_id);
                    }
                });
                return result;
            },
            getShippingMethodCode: function(){
                var methodCode = {};
                if (this.shippingMethod()) {
                    var methods = this.shippingMethod();
                    for(var i in methods) {
                        if (isNaN(i)) {
                            continue;
                        }
                        methodCode[i] = methods[i].method_code;
                    }
                }
                return JSON.stringify(methodCode);
            },
            getShippingMethodCarrierCode: function(){
                var carrierCode = {};
                if (this.shippingMethod()) {
                    var methods = this.shippingMethod();
                    for(var i in methods) {
                        if (isNaN(i)) {
                            continue;
                        }
                        carrierCode[i] = methods[i].carrier_code;
                    }
                }
                return JSON.stringify(carrierCode);
            }
        };
    }
);
