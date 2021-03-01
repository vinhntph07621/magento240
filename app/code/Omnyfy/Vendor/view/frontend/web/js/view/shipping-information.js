/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/sidebar',
        'Omnyfy_Vendor/js/model/location'
    ],
    function($, Component, quote, stepNavigator, sidebarModel, loc) {
        'use strict';
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/shipping-information'
            },

            isVisible: function() {
                return !quote.isVirtual() && stepNavigator.isProcessed('shipping');
            },

            getShippingMethodTitle: function() {
                var shippingMethod = quote.shippingMethod();
                var title ='';
                if (shippingMethod) {
                    for(var i in shippingMethod) {
                        if (title != '') {
                            title += ', ';
                        }
                        title += shippingMethod[i].carrier_title + " - " + shippingMethod[i].method_title;
                    }
                }
                return title;
            },
            getShippingMethods: function() {
                var shippingMethod = quote.shippingMethod();
                var result = [];
                if (shippingMethod) {
                    for(var i in shippingMethod) {
                        var title = shippingMethod[i].carrier_title + " - " + shippingMethod[i].method_title;
                        var location = loc.getLocationById(i);
                        if (location) {
                            result.push({title: title, location: location});
                        }
                    }
                }
                return result;
            },
            back: function() {
                sidebarModel.hide();
                stepNavigator.navigateTo('shipping');
            },

            backToShippingMethod: function() {
                sidebarModel.hide();
                stepNavigator.navigateTo('shipping', 'opc-shipping_method');
            }
        });
    }
);
