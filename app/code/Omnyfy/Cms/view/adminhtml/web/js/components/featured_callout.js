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
            var featured_callout = uiRegistry.get('index = featured_callout');
            var callout_image = uiRegistry.get('index = callout_image');
            var callout_content = uiRegistry.get('index = callout_content');
            if (featured_callout !== undefined && callout_image !== undefined && callout_content !== undefined){
                if (visibility == 1) {
                    callout_image.show();
                    callout_content.show();
                    //category_icon.validation['required-entry'] = true;
                } else {
                    callout_image.hide();
                    callout_content.hide();
                    //category_icon.validation = _.omit(category_icon.validation, 'required-entry');
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