<?php
/**
 * Project: Strip API
 * User: jing
 * Date: 2019-07-23
 * Time: 16:42
 */
namespace Omnyfy\Vendor\Model\Locator;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;

class LocationRegistryLocator implements LocationLocatorInterface
{
    private $registry;

    private $location;

    private $store;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function getLocation()
    {
        if (null !== $this->location) {
            return $this->location;
        }

        if ($location = $this->registry->registry('current_omnyfy_vendor_location')) {
            return $this->location = $location;
        }

        throw new NotFoundException(__('Location was not registered'));
    }

    public function getStore()
    {
        if (null !== $this->store) {
            return $this->store;
        }

        if ($store = $this->registry->registry('current_store')) {
            return $this->store = $store;
        }

        throw new NotFoundException(__('Store was not registered'));
    }

    public function getWebsiteIds()
    {
        return $this->getLocation()->getWebsiteIds();
    }

    public function getBaseCurrencyCode()
    {
        return $this->getStore()->getBaseCurrencyCode();
    }
}
 