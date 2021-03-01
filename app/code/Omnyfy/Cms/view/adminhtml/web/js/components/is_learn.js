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
            var is_learn = uiRegistry.get('index = is_learn');
            var category_icon = uiRegistry.get('index = category_icon');
            var category_banner = uiRegistry.get('index = category_banner');
            var is_specific_country = uiRegistry.get('index = is_specific_country');
            var country_id = uiRegistry.get('index = country_id');
            if (is_learn !== undefined && category_icon !== undefined && category_banner !== undefined) {
                if (visibility == 1) {
                    category_icon.show();
                    category_banner.show();
                    is_specific_country.show();
                    //category_icon.validation['required-entry'] = true;
                } else {
                    category_icon.hide();
                    category_banner.hide();
                    is_specific_country.hide();
                    country_id.hide();
                    is_specific_country.value(0);
                    //category_icon.validation = _.omit(category_icon.validation, 'required-entry');
                }
                if (is_learn.value() == 1) {
                    if (is_specific_country.value() == 0) {
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