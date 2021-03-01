require([
    'underscore',
    'jquery'
], function (_, $) {
    'use strict';
    if ($('#vendor_account_type_id').val() == 1) {
        $('.field-bank_address').hide();
        $('.field-swift_code').hide();
        $('#vendor_swift_code').removeClass('required-entry _required');
        $('.field-swift_code').removeClass('required _required');
    } else {
        $('.field-bank_address').show();
        $('.field-swift_code').show();
        $('#vendor_swift_code').addClass('required-entry _required');
        $('.field-swift_code').addClass('required _required');
    }
    $('#vendor_account_type_id').change(function () {
        if (this.value == 1) {
            $('.field-bank_address').hide();
            $('.field-swift_code').hide();
            $('#vendor_swift_code').removeClass('required-entry _required');
            $('.field-swift_code').removeClass('required _required');
        } else {
            $('.field-bank_address').show();
            $('.field-swift_code').show();
            $('#vendor_swift_code').addClass('required-entry _required');
            $('.field-swift_code').addClass('required _required');
        }
    });
});