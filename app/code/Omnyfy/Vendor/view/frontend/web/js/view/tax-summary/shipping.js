/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/shipping',
        'Magento_Checkout/js/model/quote'
    ],
    function ($, Component, quote) {
        var displayMode = window.checkoutConfig.reviewShippingDisplayMode;
        return Component.extend({
            defaults: {
                displayMode: displayMode,
                template: 'Magento_Tax/checkout/summary/shipping'
            },
            isBothPricesDisplayed: function() {
                var result = 'both' == this.displayMode;
                console.log("both haha", result);
                return result;
            },
            isIncludingDisplayed: function() {
                var result = 'including' == this.displayMode;
                console.log("isIncludingDisplayed", result);
                return result;
            },
            isExcludingDisplayed: function() {
                var result = 'excluding' == this.displayMode;
                console.log("isExcludingDisplayed", result);
                return result;
            },
            isCalculated: function() {
                return this.totals() && this.isFullMode() && null != quote.shippingMethod();
            },
            getIncludingValue: function() {
                if (!this.isCalculated()) {
                    return this.notCalculatedMessage;
                }
                var price =  this.totals().shipping_incl_tax;
                return this.getFormattedPrice(price);
            },
            getExcludingValue: function() {
                if (!this.isCalculated()) {
                    return this.notCalculatedMessage;
                }
                var price =  this.totals().shipping_amount;
                return this.getFormattedPrice(price);
            }
        });
    }
);
