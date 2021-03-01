define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/single-checkbox',
    'jquery'
], function (_, uiRegistry, toggle, $) {
    'use strict';
    return toggle.extend({
        /**
         * Init
         */
        initialize: function () {
            this._super();
            this.resetVisibility();
            return this;
        },
        toggleVisibilityOnRender: function (visibility, time) {
            var seller_fee = uiRegistry.get('index = seller_fee');
            var min_seller_fee = uiRegistry.get('index = min_seller_fee');
            var max_seller_fee = uiRegistry.get('index = max_seller_fee');
            var disbursement_fee = uiRegistry.get('index = disbursement_fee');
            if (seller_fee !== undefined && min_seller_fee !== undefined && max_seller_fee !== undefined && disbursement_fee !== undefined) {
                if (visibility) {
                    seller_fee.show();
                    min_seller_fee.show();
                    max_seller_fee.show();
                    disbursement_fee.show();
                } else {
                    seller_fee.value('').hide();
                    min_seller_fee.value('').hide();
                    max_seller_fee.value('').hide();
                    disbursement_fee.value('').hide();
                }

                return;
            } else {
                var self = this;
                setTimeout(function () {
                    self.toggleVisibilityOnRender(visibility, time);
                }, time);
            }
        },
        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            if (value == 1) {
                this.showField();
            } else {
                this.hideField();
            }
            return this._super();
        },
        resetVisibility: function () {
            if (this.value() == 1) {
                if(this.checked() === false ){
                   this.checked(true); 
                }
                this.showField();
            } else {
                this.hideField();
            }
        },
        showField: function () {
            this.toggleVisibilityOnRender(true, 3000);
        },
        hideField: function () {
            this.toggleVisibilityOnRender(false, 3000);
        }
    });
});