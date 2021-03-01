<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-06
 * Time: 17:03
 */
namespace Omnyfy\Vendor\Model\Vendor\Type\Locator;

interface LocatorInterface
{
    /**
     * @return \Omnyfy\Vendor\Api\Data\VendorTypeInterface
     */
    public function getVendorType();
}
 