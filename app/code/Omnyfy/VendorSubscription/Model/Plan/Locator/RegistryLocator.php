<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-08
 * Time: 16:05
 */
namespace Omnyfy\VendorSubscription\Model\Plan\Locator;

use Magento\Framework\Exception\NotFoundException;

class RegistryLocator implements LocatorInterface
{
    private $registry;

    private $plan;

    public function __construct(\Magento\Framework\Registry $registry)
    {
        $this->registry = $registry;
    }

    public function getPlan()
    {
        if (null !== $this->plan) {
            return $this->plan;
        }

        if ($plan = $this->registry->registry('current_omnyfy_subscription_plan')) {
            return $this->plan = $plan;
        }

        throw new NotFoundException(__('Subscription Plan was not registered'));
    }
}
 