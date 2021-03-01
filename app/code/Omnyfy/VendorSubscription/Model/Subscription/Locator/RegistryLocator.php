<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-09
 * Time: 16:07
 */
namespace Omnyfy\VendorSubscription\Model\Subscription\Locator;

use Magento\Framework\Exception\NotFoundException;

class RegistryLocator implements LocatorInterface
{
    private $registry;

    private $subscription;

    public function __construct(\Magento\Framework\Registry $registry)
    {
        $this->registry = $registry;
    }

    public function getSubscription()
    {
        if (null !== $this->subscription) {
            return $this->subscription;
        }

        if ($subscription = $this->registry->registry('current_omnyfy_subscription_subscription')) {
            return $this->subscription = $subscription;
        }

        throw new NotFoundException(__('Subscription was not registered'));
    }
}
 