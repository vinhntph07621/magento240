define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/single-checkbox',
    'Magento_Ui/js/modal/modal'
], function (_, uiRegistry, singleCheckbox, modal) {
    'use strict';
    return singleCheckbox.extend({
        /**
         * Init
         */
        initialize: function () {
            this._super();
            this.resetVisibility();
            return this;
        },
        toggleVisibilityOnRender: function (visibility, time) {
            var is_specific_country = uiRegistry.get('index = is_specific_country');
            var is_learn = uiRegistry.get('index = is_learn');
            var category_icon = uiRegistry.get('index = category_icon');
            var country_id = uiRegistry.get('index = country_id');
            if (is_specific_country !== undefined && country_id !== undefined && category_icon !== undefined){
                if (visibility == 1) {
                    country_id.show();
                    category_icon.hide();
                } else {
                    country_id.hide();  
					if(is_specific_country.value()==0 && is_learn.value()==1){
						category_icon.show();
					}	
                }
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
                this.showField();
            } else {
                this.hideField();
            }
        },
        showField: function () {
            this.toggleVisibilityOnRender(1, 3000);
        },
        hideField: function () {
            this.toggleVisibilityOnRender(0, 3000);
        }
    });
});