<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-09
 * Time: 16:06
 */
namespace Omnyfy\VendorSubscription\Model\Subscription\Locator;

interface LocatorInterface
{
    /**
     * @return \Omnyfy\VendorSubscription\Api\Data\SubscriptionInterface
     */
    public function getSubscription();
}
 