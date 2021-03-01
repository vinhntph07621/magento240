define([
    'ko',
    'Omnyfy_Mcm/js/view/checkout/summary/transactionfee',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/totals'
], function (ko, Component, quote, priceUtils, totals) {
    'use strict';

    var show_hide_transactionfee = window.checkoutConfig.show_hide_transactionfee;
    var fee_title = 'Transaction Fee (incl. tax)';

    return Component.extend({
        totals: ko.observable(totals),
        isTransactionFeeVisible: show_hide_transactionfee,
        transactionFeeTitle: fee_title,

        isDisplayed: function () {
            return this.isTransactionFeeVisible;
        },

        getValue: function () {
            var price = 0;

            if (this.totals() && this.totals().getSegment('mcm_transaction_fee')) {
                price = this.totals().getSegment('mcm_transaction_fee').value;
            }
            return price;
        },

        getFormattedPrice: function () {
            return priceUtils.formatPrice(this.getValue(), quote.getPriceFormat());
        }
    });
});