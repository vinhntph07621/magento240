<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-16
 * Time: 12:53
 */
namespace Omnyfy\Vendor\Model\Locator;

use Omnyfy\Vendor\Api\Data\VendorInterface;
use Magento\Store\Api\Data\StoreInterface;

interface LocatorInterface
{
    /**
     * @return VendorInterface
     */
    public function getVendor();

    /**
     * @return StoreInterface
     */
    public function getStore();

    /**
     * @return array
     */
    public function getWebsiteIds();

    /**
     * @return string
     */
    public function getBaseCurrencyCode();
}
 