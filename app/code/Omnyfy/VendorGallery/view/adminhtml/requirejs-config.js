/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            categoryForm:       'Magento_Catalog/catalog/category/form',
            newCategoryDialog:  'Magento_Catalog/js/new-category-dialog',
            categoryTree:       'Magento_Catalog/js/category-tree',
            vendorGallery:      'Omnyfy_VendorGallery/js/vendor-gallery',
            baseImage:          'Magento_Catalog/catalog/base-image-uploader',
            productAttributes:  'Magento_Catalog/catalog/product-attributes',
            openVendorVideoModal:  'Omnyfy_VendorGallery/js/video-modal',
            newVendorVideoDialog:  'Omnyfy_VendorGallery/js/new-video-dialog',
        }
    },
    deps: [
        'Magento_Catalog/catalog/product'
    ]
};
