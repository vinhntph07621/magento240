<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-09
 * Time: 15:13
 */
namespace Omnyfy\VendorSubscription\Observer;

class SubscriptionCreateSuccess implements \Magento\Framework\Event\ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        //TODO: Create Subscription with active status
    }
}
 