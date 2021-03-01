/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true*/
/*global alert*/
/**
 * Checkout adapter for customer data storage
 */
define([
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/create-shipping-address',
    'Magento_Checkout/js/action/select-shipping-address',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/action/select-payment-method',
    'Magento_Checkout/js/model/address-converter',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/action/create-billing-address',
    'underscore'
], function (
    addressList,
    quote,
    checkoutData,
    createShippingAddress,
    selectShippingAddress,
    selectShippingMethodAction,
    paymentService,
    selectPaymentMethodAction,
    addressConverter,
    selectBillingAddress,
    createBillingAddress,
    _
) {
    'use strict';
    var shippingConfiguration = window.checkoutConfig.shippingConfiguration;

    return {
        resolveEstimationAddress: function () {
            if (checkoutData.getShippingAddressFromData()) {
                var address = addressConverter.formAddressDataToQuoteAddress(checkoutData.getShippingAddressFromData());
                selectShippingAddress(address);
            } else {
                this.resolveShippingAddress();
            }
            if (quote.isVirtual()) {
                if  (checkoutData.getBillingAddressFromData()) {
                    address = addressConverter.formAddressDataToQuoteAddress(checkoutData.getBillingAddressFromData());
                    selectBillingAddress(address);
                } else {
                    this.resolveBillingAddress();
                }
            }

        },

        resolveShippingAddress: function () {
            var newCustomerShippingAddress = checkoutData.getNewCustomerShippingAddress();
            if (newCustomerShippingAddress) {
                createShippingAddress(newCustomerShippingAddress);
            }
            this.applyShippingAddress();
        },

        applyShippingAddress: function (isEstimatedAddress) {
            if (addressList().length == 0) {
                var address = addressConverter.formAddressDataToQuoteAddress(checkoutData.getShippingAddressFromData());
                selectShippingAddress(address);
            }
            var shippingAddress = quote.shippingAddress(),
                isConvertAddress = isEstimatedAddress || false,
                addressData;
            if (!shippingAddress) {
                var isShippingAddressInitialized = addressList.some(function (address) {
                    if (checkoutData.getSelectedShippingAddress() == address.getKey()) {
                        addressData = isConvertAddress
                            ? addressConverter.addressToEstimationAddress(address)
                            : address;
                        selectShippingAddress(addressData);
                        return true;
                    }
                    return false;
                });

                if (!isShippingAddressInitialized) {
                    isShippingAddressInitialized = addressList.some(function (address) {
                        if (address.isDefaultShipping()) {
                            addressData = isConvertAddress
                                ? addressConverter.addressToEstimationAddress(address)
                                : address;
                            selectShippingAddress(addressData);
                            return true;
                        }
                        return false;
                    });
                }
                if (!isShippingAddressInitialized && addressList().length == 1) {
                    addressData = isConvertAddress
                        ? addressConverter.addressToEstimationAddress(addressList()[0])
                        : addressList()[0];
                    selectShippingAddress(addressData);
                }
            }
        },

        resolveShippingRates: function (ratesData) {
            var selectedShippingMethods = checkoutData.getSelectedShippingMethodGroup();
            var availableRate = false;

            if (selectedShippingMethods) {
                selectShippingMethodAction(selectedShippingMethods);
            }

            if (ratesData.length == 1) {
                selectShippingMethodAction(this.rateToMethod(ratesData[0]));
            } else {

                // Preselect method if location has only one shipping method.
                //
                // for each shipping method
                for (var i=0; i<ratesData.length; i++) {
                    var k = 0;

                    //check if there is an other shipping method with the same location
                    for (var j=i+1; j<ratesData.length; j++) {
                        if (ratesData[i].extension_attributes['location_id'] == ratesData[j].extension_attributes['location_id']) {
                            k++; // set flag, that current location has more shipping methods
                            ratesData.splice(j); // remove found shipping method
                        }
                    }

                    // if other methods for location of current method were found, remove current method
                    if (k > 0) {
                        ratesData.splice(i);
                    }
                }

                // now we have methods with unique locations; select those locations.
                if (ratesData.length) {
                    selectShippingMethodAction(this.methodsOnly(ratesData));
                }
            }
            return;

            var methods = this.methodsOnly(ratesData);
            if (methods) {
                selectShippingMethodAction(methods);
                return;
            }

            if (quote.shippingMethod()) {
                if (this.checkRatesData(quote.shippingMethod(), ratesData)) {
                    availableRate = quote.shippingMethod();
                }
            }

            if (!availableRate && selectedShippingRate) {
                if (this.checkRatesData(selectedShippingRate, ratesData)) {
                    availableRate = selectedShippingRate;
                }
            }

            if (!availableRate && window.checkoutConfig.selectedShippingMethod) {
                if (this.checkRatesData(window.checkoutConfig.selectedShippingMethod, ratesData)) {
                    availableRate = window.checkoutConfig.selectedShippingMethod;
                    selectShippingMethodAction(window.checkoutConfig.selectedShippingMethod);
                }
            }

            //Unset selected shipping method if not available
            if (!availableRate || !this.checkAllLocation(availableRate)) {
                selectShippingMethodAction(null);
            } else {
                selectShippingMethodAction(availableRate);
            }
        },


        resolvePaymentMethod: function () {
            var availablePaymentMethods = paymentService.getAvailablePaymentMethods();
            var selectedPaymentMethod = checkoutData.getSelectedPaymentMethod();
            if (selectedPaymentMethod) {
                availablePaymentMethods.some(function (payment) {
                    if (payment.method == selectedPaymentMethod) {
                        selectPaymentMethodAction(payment);
                    }
                });
            }
        },

        resolveBillingAddress: function () {
            var selectedBillingAddress = checkoutData.getSelectedBillingAddress(),
                newCustomerBillingAddressData = checkoutData.getNewCustomerBillingAddress(),
                shippingAddress = quote.shippingAddress();

            if (selectedBillingAddress) {
                if (selectedBillingAddress == 'new-customer-address' && newCustomerBillingAddressData) {
                    selectBillingAddress(createBillingAddress(newCustomerBillingAddressData));
                } else {
                    addressList.some(function (address) {
                        if (selectedBillingAddress == address.getKey()) {
                            selectBillingAddress(address);
                        }
                    });
                }
            } else {
                this.applyBillingAddress()
            }
        },
        applyBillingAddress: function () {
            if (quote.billingAddress()) {
                selectBillingAddress(quote.billingAddress());
                return;
            }
            var shippingAddress = quote.shippingAddress();
            if (shippingAddress
                && shippingAddress.canUseForBilling()
                && (shippingAddress.isDefaultShipping() || !quote.isVirtual())) {
                //set billing address same as shipping by default if it is not empty
                selectBillingAddress(quote.shippingAddress());
            }
        },
        checkRatesData: function(list, ratesData){
            var allFound = true;
            var count = 0;
            for(var i in list) {
                var item = list[i];
                count++;
                var oneFound = _.find(ratesData, function(rate){
                    return rate.carrier_code == item.carrier_code
                    && rate.method_code == item.method_code
                    && rate.extension_attributes.location_id == i;
                });
                if (!oneFound) {
                    allFound = false;
                }
            }
            return (count > 0) && allFound;
        },
        checkAllLocation: function(selectedRates){
            var locationIds = quote.getLocationIds();
            if (0==locationIds.length) return false;

            for(var i=0; i< locationIds.length; i++) {
                var id = locationIds[i];
                if (!(id in selectedRates)) {
                    return false;
                }
            }
            return true;
        },
        rateToMethod: function(rate) {
            var result ={};
            var locationId = rate.extension_attributes.location_id;
            result[locationId] = rate;
            return result;
        },
        methodsOnly: function(ratesData) {
            //TODO: go through all locations for ratesData,
            //TODO: if all location only got one method, select them as shipping methods
            var locationIds = quote.getLocationIds();
            var counts = {};
            var result = {};
            for(var i in ratesData){
                var rate = ratesData[i];
                var lid = rate.extension_attributes.location_id;
                if (locationIds.indexOf(lid) < 0) {
                    continue;
                }
                if (lid in counts) {
                    counts[lid] ++;
                }
                else{
                    counts[lid] = 1;
                }
                result[lid] = rate;
            }

            for(var i=0; i<locationIds.length; i++) {
                var lid = locationIds[i];
                if (lid in counts) {
                    if (counts[lid] > 1) {
                        return false;
                    }
                }
                else{
                    return false;
                }
            }
            return result;
        }
    }
});
