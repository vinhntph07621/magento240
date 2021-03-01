define([
    'underscore',
    'uiRegistry',
    //'Magento_Ui/js/form/element/country',
    'Magento_Ui/js/form/element/ui-select',
    'jquery'
], function (_, uiRegistry, select, $) {
    'use strict';

    return select.extend({
        /**
         * Init
         */
        initialize: function () {
            this._super();

            //this.resetVisibility();

            return this;
        },
        toggleVisibilityOnRender: function (fieldValue, time) {
            var field = uiRegistry.get('index = country_name');
            var field1 = uiRegistry.get('index = country_id');
            if (field !== undefined && field1 !== undefined) {
                if (fieldValue != '') {
                    var selected = this.getSelected();
                    var text = selected.map(function (option) {
                        return option.label;
                    });
                    field.value(text[0]);
                } else {
                    //field.value('');
                }

                return;
            } else {
                var self = this;
                setTimeout(function () {
                    self.toggleVisibilityOnRender(fieldValue, time);
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