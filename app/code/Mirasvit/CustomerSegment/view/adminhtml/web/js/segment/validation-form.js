define([
    'Magento_Ui/js/form/components/insert-form',
    'uiRegistry',
    'underscore'
], function (InsertForm, Registry, _) {
    'use strict';
    
    return InsertForm.extend({
        updateData: function (params) {
            params = this.getParams(params);
            
            if (params) {
                return this._super(params);
            }
            
            return this;
        },
        
        render: function (params) {
            return this._super(this.getParams(params));
        },
        
        getParams: function (params) {
            var input = Registry.get('customersegment_segment_form.customersegment_segment_form.conditions.validation.customer_ids');
            var customers = input.value();
            
            if (!customers) {
                return null;
            }
            
            params = _.extend(params || {}, {customers: customers});
            
            return params;
        }
    });
});
