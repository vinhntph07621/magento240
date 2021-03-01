/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define,alert*/
define(
    [
        '../model/quote'
    ],
    function (quote) {
        "use strict";
        return function (shippingMethod, vendorLocation) {

        	var groupData = quote.shippingMethodGroup();
        	
        	groupData[vendorLocation] = shippingMethod;

            quote.shippingMethodGroup(groupData);
        }
    }
);
