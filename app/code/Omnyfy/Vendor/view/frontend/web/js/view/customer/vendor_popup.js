define([
    'jquery',
    'mage/mage',
    'Magento_Customer/js/customer-data'
], function ($, mage, customerData) {
    'use strict';

    return function (config, element) {
        $(element).mage('validation', {
            errorPlacement: function (error, element) {
                element.after(error);
            }
        });

        $('#vendor-form').submit(function(event){
            event.preventDefault();
            $('body').trigger('processStart');
            $bootstrap.modal.call($('#vendor-modal'), 'hide');
            $.ajax({
                method: 'POST',
                url: '/rest/V1/vendors/bind',
                data: JSON.stringify({vendorId: $('#vendor_id').val() }),
                dataType: "json",
                contentType: 'application/json',
                processData: false
            })
            .done(function (msg){
                console.log(msg);
            })
            .fail(function (msg){
                console.log(msg);
            })
            .always(function(){
                location.reload();
                $('body').trigger('processStop');
                customerData.invalidate(['my_vendor']);
                customerData.reload(['my_vendor'], true);
            });
            //customerData.invalidate(['my_vendor']);
            //customerData.reload(['my_vendor'], true);
            return false;
        });
    };
});
