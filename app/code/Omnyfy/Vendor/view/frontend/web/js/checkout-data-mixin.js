define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'jquery/jquery-storageapi'
], function ($, storage) {
    'use strict';

    var cacheKey = 'checkout-data',

        /**
         * @param {Object} data
         */
        saveData = function (data) {
            storage.set(cacheKey, data);
        },

        /**
         * @return {*}
         */
        getData = function () {
            var data = storage.get(cacheKey)();

            if ($.isEmptyObject(data)) {
                data = $.initNamespaceStorage('mage-cache-storage').localStorage.get(cacheKey);

                if ($.isEmptyObject(data)) {
                    data = initData();
                }

                saveData(data);
            }

            return data;
        };

    return function (checkoutData) {
        //console.log(checkoutData);
        var mixin = {

            setSelectedShippingMethodGroup: function (data) {
                var obj = getData();

                obj.selectedShippingMethodGroup = data;
                saveData(obj);
            },

            /**
             * Pulling the selected shipping rate from local storage
             *
             * @return {*}
             */
            getSelectedShippingMethodGroup: function () {
                return getData().selectedShippingMethodGroup;
            }

        };

        return $.extend(checkoutData, mixin);
    };
})