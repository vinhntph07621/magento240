/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'jquery',
    'mage/mage'
], function ($, mage) {
    'use strict';

    return function (config, element) {
        $(element).mage('validation', {
            errorPlacement: function (error, element) {

                if (element.parents('#vendor-review-table').length) {
                    $('#vendor-review-table').siblings(this.errorElement + '.' + this.errorClass).remove();
                    $('#vendor-review-table').after(error);
                } else {
                    element.after(error);
                }
            }
        });
    };
});
