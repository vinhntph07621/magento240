define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/country',
    'jquery'
], function (_, uiRegistry, select, $) {
    'use strict';

    return select.extend({
        /**
         * Init
         */
        initialize: function () {
            this._super();

            this.resetVisibility();

            return this;
        },

        toggleVisibilityOnRender: function (fieldValue, time) {
            var field = uiRegistry.get('index = country_name');
            if(field !== undefined) {
                var test = $('[name="country_id"] option[value='+fieldValue+']').text();
                //alert(test);
                if(test) {
                    field.value(test);
                } else {
                    //field.value('');
                }

                return;
            }
            else {
                var self = this;
                setTimeout(function() {
                    self.toggleVisibilityOnRender(this.value(), time);
                }, time);
            }
        },

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {
            if (value) {
                this.showField();
            } else {
                this.hideField();
            }
            return this._super();
        },

        resetVisibility: function () {
            if (this.value()) {
                this.showField();
            } else {
                this.hideField();
            }
        },

        showField: function () {
            this.toggleVisibilityOnRender(this.value(), 3000);

        },

        hideField: function () {
            this.toggleVisibilityOnRender(0, 3000);
        }
    });
});