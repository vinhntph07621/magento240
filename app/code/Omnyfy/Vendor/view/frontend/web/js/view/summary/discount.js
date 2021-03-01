/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote'
    ],
    function (Component, quote) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Magento_SalesRule/summary/discount'
            },
            totals: quote.getTotals(),
            isDisplayed: function() {
                return this.isFullMode() && this.getPureValue() != 0;
            },
            getCouponCode: function() {
                if (!this.totals()) {
                    return null;
                }
                return this.totals()['coupon_code'];
            },
            /**
             * Get discount title
             *
             * @returns {null|String}
             */
            getTitle: function () {
                var discountSegments;

                if (!this.totals()) {
                    return null;
                }

                discountSegments = this.totals()['total_segments'].filter(function (segment) {
                    return segment.code.indexOf('discount') !== -1;
                });

                return discountSegments.length ? discountSegments[0].title : null;
            },
            getPureValue: function() {
                var price = 0;
                if (this.totals() && this.totals().discount_amount) {
                    price = parseFloat(this.totals().discount_amount);
                }
                return price;
            },
            getValue: function() {
                return this.getFormattedPrice(this.getPureValue());
            }
        });
    }
);
