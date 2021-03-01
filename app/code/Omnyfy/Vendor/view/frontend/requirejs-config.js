/**
 * Created by jing on 4/5/17.
 */

var config = {
    map: {
        '*': {
            'Magento_Checkout/js/view/cart/shipping-rates':
                'Omnyfy_Vendor/js/view/cart/shipping-rates',

            'Magento_Checkout/template/cart/shipping-rates.html':
                'Omnyfy_Vendor/template/cart/shipping-rates.html',

            'Magento_Checkout/js/model/shipping-rate-processor/customer-address':
                'Omnyfy_Vendor/js/model/shipping-rate-processor/customer-address',

            'Magento_Checkout/js/model/shipping-rate-processor/new-address':
                'Omnyfy_Vendor/js/model/shipping-rate-processor/new-address',

            'Magento_Checkout/js/model/shipping-save-processor/default':
                'Omnyfy_Vendor/js/model/shipping-save-processor/default',

            'Magento_Checkout/js/model/checkout-data-resolver':
                'Omnyfy_Vendor/js/model/checkout-data-resolver',

            'Magento_Checkout/js/view/shipping':
                'Omnyfy_Vendor/js/view/shipping',

            'Magento_Checkout/js/view/summary/shipping':
                'Omnyfy_Vendor/js/view/summary/shipping',

            'Magento_Checkout/js/view/summary/cart-items':
                'Omnyfy_Vendor/js/view/summary/cart-items',

            'Magento_Checkout/js/view/shipping-information':
                'Omnyfy_Vendor/js/view/shipping-information',

            'Magento_Checkout/template/shipping.html':
                'Omnyfy_Vendor/template/shipping.html',

            'Magento_Checkout/template/shipping-information.html':
                'Omnyfy_Vendor/template/shipping-information.html',

            'Magento_Checkout/template/summary/cart-items.html':
                'Omnyfy_Vendor/template/summary/cart-items.html',

            'Magento_Checkout/js/model/quote':
                'Omnyfy_Vendor/js/model/quote',

            'Magento_Tax/template/checkout/cart/totals/shipping.html':
                'Omnyfy_Vendor/template/cart/totals/shipping.html',

            'Magento_Checkout/js/model/cart/estimate-service':
                'Omnyfy_Vendor/js/model/cart/estimate-service',

            'Magento_SalesRule/js/view/summary/discount':
                'Omnyfy_Vendor/js/view/summary/discount',

            'Magento_SalesRule/template/cart/totals/discount.html':
                'Omnyfy_Vendor/template/cart/totals/discount.html',

            'Magento_SalesRule/template/summary/discount.html':
                'Omnyfy_Vendor/template/summary/discount.html',

            'Magento_Swatches/js/swatch-renderer':
                'Omnyfy_Vendor/js/swatch-renderer',

            'Magento_Tax/template/checkout/cart/totals/grand-total.html':
            'Omnyfy_Vendor/template/cart/totals/grand-total.html',

            'Magento_Tax/js/view/checkout/summary/grand-total':
            'Omnyfy_Vendor/js/view/checkout/summary/grand-total',

            configurable:
                'Omnyfy_Vendor/js/configurable'
        }
    },
    deps: [
        'Magento_Checkout/js/shopping-cart'
    ],
    paths: {
        'jquery.bootstrap': 'Omnyfy_Vendor/js/bootstrap.min'
    },
    shim: {
        'jquery.bootstrap': ['jquery', 'jquery/ui']
    },
    config: {
        mixins: {
            'Magento_Checkout/js/checkout-data': {
                'Omnyfy_Vendor/js/checkout-data-mixin': true
            }
        }
    }
};
