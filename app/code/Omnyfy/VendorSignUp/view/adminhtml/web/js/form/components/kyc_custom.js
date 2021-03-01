require([
    'underscore',
    'jquery'
], function (_, $) {
    'use strict';
    
    
	initialize: function () {
		var placeSearch, autocomplete, autocomplete_textarea;
		var componentForm = {
			street_number: 'short_name',
			route: 'long_name',
			locality: 'long_name',
			administrative_area_level_1: 'long_name',
			country: 'short_name',
			postal_code: 'short_name'
		};
        // Create the autocomplete object, restricting the search
        // to geographical location types.
        setTimeout(function () {
            autocomplete = new google.maps.places.Autocomplete(
                    (document.getElementsByName("business_address")[0]),
                    {types: ['geocode']});
            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                var place = autocomplete.getPlace();


                var addressDetail = {};
                // Get each component of the address from the place details
                // and fill the corresponding field on the form.
                for (var i = 0; i < place.address_components.length; i++) {
                    var addressType = place.address_components[i].types[0];

                    if (componentForm[addressType]) {
                        //alert(addressType);
                        var val = place.address_components[i][componentForm[addressType]];
                        //alert(val);
                        addressDetail[addressType] = val;
                    }
                }

                console.log(addressDetail);
                document.getElementsByName("city")[0].value = (typeof addressDetail.locality === 'undefined') ? '' : addressDetail.locality;
                document.getElementsByName("postcode")[0].value = (typeof addressDetail.postal_code === 'undefined') ? '' : addressDetail.postal_code;
                document.getElementsByName("state")[0].value = (typeof addressDetail.administrative_area_level_1 === 'undefined') ? '' : addressDetail.administrative_area_level_1;
                document.getElementById("country").value = (typeof addressDetail.country == 'undefined') ? '' : addressDetail.country;
				
				var taxNumber  = {
					US: ["EIN"],
					AU: ["ABN", "ACN"],
					NZ: ["NZBN", "NZCN"],
					ZA: ["CIPC", "SARSNZ"]
				}
				var taxNumberArr = ["US", "AU", "NZ", "ZA"];
				
				if (taxNumberArr.includes(addressDetail.country)){
					document.getElementById('tax_number').style.display  = 'block';
					/* document.getElementById('tax_number_other').style.display  = 'none'; */
					var taxNumberOptions = "<option value=''>Tax Name</option>";
					for (categoryId in taxNumber[addressDetail.country]) {
						taxNumberOptions += "<option>" + taxNumber[addressDetail.country][categoryId] + "</option>";
					}
					document.getElementById("tax_number").innerHTML = taxNumberOptions;
				}
				else {
					/* document.getElementById('tax_number_other').style.display  = 'block'; */
					document.getElementById('tax_number').style.display  = 'none';
				}
            });
        }, 23000);
    }

    /* if ($('#vendor_account_type_id').val() == 1) {
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
    }); */
});