/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'jquery',
        "underscore",
        'Magento_Ui/js/form/form',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'Omnyfy_Vendor/js/model/location',
        'Magento_Checkout/js/action/create-shipping-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-address/form-popup-state',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Ui/js/modal/modal',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Checkout/js/checkout-data',
        'uiRegistry',
        'mage/translate',
        'Magento_Checkout/js/model/shipping-rate-service'
    ],
    function(
        $,
        _,
        Component,
        ko,
        customer,
        addressList,
        addressConverter,
        quote,
        loc,
        createShippingAddress,
        selectShippingAddress,
        shippingRatesValidator,
        formPopUpState,
        shippingService,
        selectShippingMethodAction,
        rateRegistry,
        setShippingInformationAction,
        stepNavigator,
        modal,
        checkoutDataResolver,
        checkoutData,
        registry,
        $t
    ) {
        'use strict';
        var popUp = null;
        var locations = ko.observable(loc.getLocations());
        var shippingConfiguration = window.checkoutConfig.shippingConfiguration;
        var messageContentShipping = window.checkoutConfig.messageContentShipping;
        var isMessageContentEnable = window.checkoutConfig.isMessageContentEnable;
        var shippingMethodEnabled = window.checkoutConfig.shippingMethodEnabled;
        var overallShippingId = window.checkoutConfig.shippingOverallId;
        var shippingNotesCount = 0;

        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/shipping'
            },
            messageContent: messageContentShipping,
            shippingNotes: ko.observable(),
            messageVisible: ko.observable(false),
            shippingNotesVisible: ko.observable(false),
            shippingOptionNotesVisible: ko.observable(false),
            visible: ko.observable(!quote.isVirtual()),
            errorValidationMessage: ko.observable(false),
            isCustomerLoggedIn: customer.isLoggedIn,
            isFormPopUpVisible: formPopUpState.isVisible,
            isFormInline: addressList().length == 0,
            isNewAddressAdded: ko.observable(false),
            saveInAddressBook: true,
            quoteIsVirtual: quote.isVirtual(),
            hasErrorMsg: ko.observable(false),
            initialize: function () {
                var self = this,
                    hasNewAddress,
                    fieldsetName = 'checkout.steps.shipping-step.shippingAddress.shipping-address-fieldset';

                this._super();
                shippingRatesValidator.initFields(fieldsetName);

                if (!quote.isVirtual()) {
                    stepNavigator.registerStep(
                        'shipping',
                        '',
                        'Shipping',
                        this.visible, _.bind(this.navigate, this),
                        10
                    );
                }
                checkoutDataResolver.resolveShippingAddress();

                hasNewAddress = addressList.some(function (address) {
                    return address.getType() == 'new-customer-address';
                });

                this.isNewAddressAdded(hasNewAddress);

                this.isFormPopUpVisible.subscribe(function (value) {
                    if (value) {
                        self.getPopUp().openModal();
                    }
                });

                quote.shippingMethod.subscribe(function (newVal) {
                    if(newVal.length) {
                        self.errorValidationMessage(false);
                    }
                });

                registry.async('checkoutProvider')(function (checkoutProvider) {
                    var shippingAddressData = checkoutData.getShippingAddressFromData();
                    if (shippingAddressData) {
                        checkoutProvider.set(
                            'shippingAddress',
                            $.extend({}, checkoutProvider.get('shippingAddress'), shippingAddressData)
                        );
                    }
                    checkoutProvider.on('shippingAddress', function (shippingAddressData) {
                        checkoutData.setShippingAddressFromData(shippingAddressData);
                    });
                });

                return this;
            },

            navigate: function (step) {
                step && step.isVisible(true);
            },

            getPopUp: function () {
                var self = this;
                if (!popUp) {
                    var buttons = this.popUpForm.options.buttons;
                    this.popUpForm.options.buttons = [
                        {
                            text: buttons.save.text ? buttons.save.text : $t('Save Address'),
                            class: buttons.save.class ? buttons.save.class : 'action primary action-save-address',
                            click: self.saveNewAddress.bind(self)
                        },
                        {
                            text: buttons.cancel.text ? buttons.cancel.text: $t('Cancel'),
                            class: buttons.cancel.class ? buttons.cancel.class : 'action secondary action-hide-popup',
                            click: function() {
                                this.closeModal();
                            }
                        }
                    ];
                    this.popUpForm.options.closed = function() {
                        self.isFormPopUpVisible(false);
                    };
                    popUp = modal(this.popUpForm.options, $(this.popUpForm.element));
                }
                return popUp;
            },

            /** Show address form popup */
            showFormPopUp: function() {
                this.isFormPopUpVisible(true);
            },


            /** Save new shipping address */
            saveNewAddress: function() {
                this.source.set('params.invalid', false);
                this.source.trigger('shippingAddress.data.validate');

                if (!this.source.get('params.invalid')) {
                    var addressData = this.source.get('shippingAddress');
                    addressData.save_in_address_book = this.saveInAddressBook ? 1 : 0;

                    // New address must be selected as a shipping address
                    var newShippingAddress = createShippingAddress(addressData);
                    selectShippingAddress(newShippingAddress);
                    checkoutData.setSelectedShippingAddress(newShippingAddress.getKey());
                    checkoutData.setNewCustomerShippingAddress(addressData);
                    this.getPopUp().closeModal();
                    this.isNewAddressAdded(true);
                }
            },

            /** Shipping Method View **/
            rates: shippingService.getShippingRates(),

            getLocations: function() {
                return locations;
            },

            locationIds: ko.observable(quote.getLocationIds()),
            isLoading: shippingService.isLoading,
            isSelected: function (locationId) {
                if (quote.shippingMethod() && (locationId in quote.shippingMethod())) {
                    if (shippingConfiguration == 'overall_cart' && overallShippingId) {
                        locationId = overallShippingId;
                    }
                    var method = quote.shippingMethod()[locationId];
                    return method.carrier_code + '_' + method.method_code;
                }
                return null;
            },
            isOnly: function(locationId) {
                var count=0;
                _.each(this.rates(), function(rate){
                    if (locationId == rate.extension_attributes.location_id) {
                        count++
                    }
                });
                return count == 1;
            },
            
            groupedRates: function(locationId){
                var result =[];
                var errorNum = 0;
                var self = this;
                var shippingNotesValue = ''; 

                _.each(this.rates(), function(rate){
                    if (shippingConfiguration == 'overall_cart' && overallShippingId) {
                        locationId = overallShippingId;
                    }

                    if (locationId == rate.extension_attributes.location_id) {
                        result.push(rate);
                        if(rate.error_message.length > 0) {
                            errorNum ++;
                        }
                    }
                    
                    if(rate.extension_attributes.shipping_option_note && rate.extension_attributes.shipping_option_note !== null && rate.method_code !== null){
                        shippingNotesValue = rate.extension_attributes.shipping_option_note;
                        shippingNotesCount++;
                    }
                });

                if(shippingNotesCount > 0 ){
                    self.getShippingOptionNotes(shippingNotesValue);
                }else{
                    self.getShippingOptionNotes(null);
                }
                
                if(messageContentShipping !== null){
                    if(shippingMethodEnabled.length > 0){
                        if(isMessageContentEnable == 1){
                            self.getMessageContent();
                        }
                    }else{
                        self.messageVisible(false);
                    }
                }else{
                    self.messageVisible(false);
                }

                this.hasErrorMsg(errorNum > 0 ? true : false);

                return result;
            },

            getMessageContent: function () {
                this.messageVisible(true);
            },

            getShippingOptionNotes: function(noteShipping){
                if(noteShipping){
                    this.shippingNotesVisible(true);
                }else{
                    this.shippingNotesVisible(false);
                }
                this.shippingNotes(noteShipping);
            },

            selectShippingMethod: function(shippingMethod) {
                //marge shippingMethod data in checkoutData, if all location selected, call quote.shippingMethod to set method
                var selectedRate = checkoutData.getSelectedShippingRate();
                var locationId = shippingMethod.extension_attributes.location_id;
                selectedRate = (null == selectedRate) ? {} : selectedRate;
                if ('method_code' in selectedRate) {
                    var lid = selectedRate.extension_attributes.location_id;
                    var tmp = {};
                    tmp[lid] = selectedRate;
                    selectedRate = tmp;
                }
                selectedRate[locationId] = shippingMethod;
                if (checkoutDataResolver.checkAllLocation(selectedRate)) {
                    selectShippingMethodAction(selectedRate);
                }
                checkoutData.setSelectedShippingRate(selectedRate);

                return true;
            },

            setShippingInformation: function () {
                if (this.validateShippingInformation()) {
                    setShippingInformationAction().done(
                        function() {
                            stepNavigator.next();
                        }
                    );
                }
            },

            validateShippingInformation: function () {
                var shippingAddress,
                    addressData,
                    loginFormSelector = 'form[data-role=email-with-possible-login]',
                    emailValidationResult = customer.isLoggedIn();

                if (_.isEmpty(quote.shippingMethod())) {
                    this.errorValidationMessage('Please specify a shipping method');
                    return false;
                }

                if (!customer.isLoggedIn()) {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }

                if (!emailValidationResult) {
                    $(loginFormSelector + ' input[name=username]').focus();
                }

                if (this.isFormInline) {
                    this.source.set('params.invalid', false);
                    this.source.trigger('shippingAddress.data.validate');
                    if (this.source.get('shippingAddress.custom_attributes')) {
                        this.source.trigger('shippingAddress.custom_attributes.data.validate');
                    };
                    if (this.source.get('params.invalid')
                        || !checkoutDataResolver.checkAllLocation(quote.shippingMethod())
                        || !emailValidationResult
                    ) {
                        return false;
                    }
                    shippingAddress = quote.shippingAddress();
                    addressData = addressConverter.formAddressDataToQuoteAddress(
                        this.source.get('shippingAddress')
                    );

                    //Copy form data to quote shipping address object
                    for (var field in addressData) {
                        if (addressData.hasOwnProperty(field)
                            && shippingAddress.hasOwnProperty(field)
                            && typeof addressData[field] != 'function'
                        ) {
                            shippingAddress[field] = addressData[field];
                        }
                    }

                    if (customer.isLoggedIn()) {
                        shippingAddress.save_in_address_book = 1;
                    }
                    selectShippingAddress(shippingAddress);
                }
                return true;
            }
        });
    }
);