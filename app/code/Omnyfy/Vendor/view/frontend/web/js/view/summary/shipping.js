/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, Component, quote) {
        return Component.extend({
            defaults: {
                template: 'Magento_Checkout/summary/shipping'
            },
            quoteIsVirtual: quote.isVirtual(),
            totals: quote.getTotals(),
            getShippingMethodTitle: function() {
                if (!this.isCalculated()) {
                    return '';
                }
                var shippingMethod = quote.shippingMethod();
                var title = '';
                for(var locationId in shippingMethod) {
                    if (title != '') {
                        title += ', ' + "\n";
                    }
                    title += shippingMethod[locationId].carrier_title + " _ " + shippingMethod[locationId].method_title;
                }
                return ' - ';
                //return shippingMethod ? shippingMethod.carrier_title + " - " + shippingMethod.method_title : '';
            },
            isCalculated: function() {
                return this.totals() && this.isFullMode() && null != quote.shippingMethod();
            },
            getValue: function() {
                if (!this.isCalculated()) {
                    return this.notCalculatedMessage;
                }
                var price =  this.totals().shipping_amount;
                return this.getFormattedPrice(price);
            },
            getShippingValue: function(){

                var shippingGroup = quote.shippingMethodGroup(),
                    shippingTotal = 0;

                for(var locationId in shippingGroup){
                    if (shippingGroup.hasOwnProperty(locationId)) {
                        console.log(shippingGroup[locationId].amount);
                        shippingTotal += shippingGroup[locationId].amount;
                    }
                }

                return this.getFormattedPrice(shippingTotal);
            }
        });
    }
);
