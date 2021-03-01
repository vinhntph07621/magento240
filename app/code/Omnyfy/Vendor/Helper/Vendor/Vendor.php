<?php

namespace Omnyfy\Vendor\Helper\Vendor;

/**
 * Class Data
 * @package Omnyfy\Vendor\Helper\Vendor
 */
class Data
{
    /**
     * @var \Omnyfy\Vendor\Model\VendorFactory
     */
    protected $vendorFactory;

    /**
     * Data constructor.
     * @param \Omnyfy\Vendor\Model\VendorFactory $vendorFactory
     */
    public function __construct(
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory
    ) {
        $this->vendorFactory = $vendorFactory;
    }

    /**
     * Get vendor data by vendor ID
     * @param $vendorId
     * @return bool|\Omnyfy\Vendor\Model\Vendor
     */
    public function getVendorData($vendorId)
    {
        $vendor = $this->vendorFactory->create()->load($vendorId);

        if ($vendor->getId()) {
            return $vendor;
        }

        return false;
    }
}
