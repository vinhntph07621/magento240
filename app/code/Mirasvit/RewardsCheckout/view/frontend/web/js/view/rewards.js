define([
    'uiComponent',
    'Magento_Customer/js/customer-data'
], function (Component, customerData) {
    'use strict';

    customerData.reload(['rewards'], true);

    return Component.extend({
        initialize: function () {
            this._super();

            this.rewards = customerData.get('rewards');
        }
    });
});
