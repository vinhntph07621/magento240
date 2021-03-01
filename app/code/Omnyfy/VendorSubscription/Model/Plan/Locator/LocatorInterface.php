<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-08
 * Time: 16:03
 */
namespace Omnyfy\VendorSubscription\Model\Plan\Locator;

interface LocatorInterface
{
    /**
     * @return \Omnyfy\VendorSubscription\Api\Data\PlanInterface
     */
    public function getPlan();
}
 