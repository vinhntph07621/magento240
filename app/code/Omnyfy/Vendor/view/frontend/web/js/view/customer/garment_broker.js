define([
    'jquery',
    'mage/mage'
], function ($, mage) {
    'use strict';

    return function (config, element) {
        $(element).mage('validation', {
            errorPlacement: function (error, element) {
                element.after(error);
            }
        });

        $('.broker-form').submit(function(event) {
            event.preventDefault();
            $('body').trigger('processStart');
            var redirect_url = $(this).find(".product_redirect_url");
            // alert(redirect_url.val());
            $.ajax({
                method: 'POST',
                url: '/rest/V1/vendors/bind',
                data: JSON.stringify({vendorId: $(this).find("[name='vendor_id']").val() }),
                dataType: "json",
                contentType: 'application/json',
                processData: false
            })
                .done(function (msg) {
                    console.log(msg);
                })
                .fail(function (msg) {
                    console.log(msg);
                })
                .always(function() {
                    $('body').trigger('processStop');
                    window.location.replace(redirect_url.val());
                });
            //customerData.invalidate(['my_vendor']);
            //customerData.reload(['my_vendor'], true);
            return false;
        });
    };
});
