<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 24/2/18
 * Time: 2:21 AM
 */
namespace Omnyfy\Vendor\Plugin\Sales\Model;

class Order
{
    protected $vendorHelper;

    public function __construct(
        \Omnyfy\Vendor\Helper\Data $helper
    )
    {
        $this->vendorHelper = $helper;
    }

    public function aroundSetShippingMethod($subject, callable $process, $shippingMethod)
    {
        if (is_array($shippingMethod)) {
            $shippingMethod = $this->vendorHelper->shippingMethodArrayToString($shippingMethod);
        }
        return $process($shippingMethod);
    }
}