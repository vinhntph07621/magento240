define([
    'Magento_Ui/js/grid/listing',
    'jquery'
], function (Listing, $) {
    'use strict';
    
    return Listing.extend({
        initialize: function () {
            this._super();
            
            $('[data-component]').hide();
            
            return this;
        },
        
        percent: function (a, b) {
            if (a > 0 && b > 0) {
                return Math.round((b / a) * 100) + '%';
            }
        }
    });
});