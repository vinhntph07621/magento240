<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 12/9/19
 * Time: 10:09 pm
 */
namespace Omnyfy\VendorSubscription\Model\Update\Locator;

use Magento\Framework\Exception\NotFoundException;

class RegistryLocator implements LocatorInterface
{
    private $registry;

    /**
     * @var \Omnyfy\VendorSubscription\Api\Data\UpdateInterface
     */
    private $update;

    public function __construct(\Magento\Framework\Registry $registry)
    {
        $this->registry = $registry;
    }

    public function getUpdate()
    {
        if (null !== $this->update) {
            return $this->update;
        }

        if ($update = $this->registry->registry('current_omnyfy_subscription_update')) {
            return $this->update = $update;
        }

        throw new NotFoundException(__('Subscription update was not registered'));
    }
}
 