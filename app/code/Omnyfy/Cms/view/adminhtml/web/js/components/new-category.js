/**
 * Copyright © 2016 Ihor Vansach (ihor@omnyfy.com). All rights reserved.
 * See LICENSE.txt for license details (http://opensource.org/licenses/osl-3.0.php).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

define([
    'Magento_Catalog/js/components/new-category'
], function (category) {
    'use strict';

    return category.extend({
        initialize: function () {
            this._super();
            this.setDefaultValue();
            return this;
        },
        setDefaultValue: function () {
            var treeParent = window.treeParent;
            //console.log(treeParent);
            if (treeParent) {
                this.value(treeParent);
            }
        },
    });
});
