define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal'
], function (_, uiRegistry, select, modal) {
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
        toggleVisibilityOnRender: function (visibility, time) {
            var link_type = uiRegistry.get('index = link_type');
            var link_url = uiRegistry.get('index = link');
            var upload_template = uiRegistry.get('index = upload_template');
			//alert(visibility+'---'+link_type.value()+'---'+link_url.value()+'---'+upload_template.value());
            if (link_type !== undefined && link_url !== undefined && upload_template !== undefined){
				if(visibility){
					link_url.show();
					upload_template.hide();
					upload_template.validation['required-entry'] = false;
					link_url.validation['required-entry'] = true;
				} else{
					upload_template.show();
					link_url.hide();
					link_url.validation['required-entry'] = false;
					upload_template.validation['required-entry'] = true;
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