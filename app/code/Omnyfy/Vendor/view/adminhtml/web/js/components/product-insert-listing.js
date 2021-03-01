define([
    'Magento_Ui/js/lib/view/utils/async',
    'uiRegistry',
    'underscore',
    'Magento_Ui/js/form/components/insert-listing'
], function ($, registry, _, InsertListing) {
    'use strict';

    return InsertListing.extend({
        defaults: {
            addProductUrl: '',
            productIds: '',
            locationId: 0,
            formProvider: '',
            modules: {
                form: '${ $.formProvider }',
                modal: '${ $.parentName }'
            }
        },

        /**
         * Render attribute
         */
        render: function () {
            this._super();
        },

        /**
         * Save attribute
         */
        save: function () {
            this.addSelectedProducts();
            this._super();
        },

        /**
         * Add selected products
         */
        addSelectedProducts: function () {
            $('body').loader('show');
            $.ajax({
                url: this.addProductUrl,
                type: 'POST',
                dataType: 'json',
                data: {
                    productIds: this.selections().getSelections(),
                    locationId: this.locationId,
                    componentJson: 1
                },
                success: function () {
                    var params = [];
                    var t = registry.get('omnyfy_vendor_inventory_listing.omnyfy_vendor_inventory_listing_data_source');
                    if (t && typeof t === 'object') {
                        t.set('params.t', Date.now());
                    }
                }.bind(this),
                complete:function() {
                    $('body').loader('hide');
                }
            });
        }
    });
});
