<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-16
 * Time: 12:55
 */
namespace Omnyfy\Vendor\Model\Locator;

use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;

class RegistryLocator implements LocatorInterface
{
    private $registry;

    private $vendor;

    private $store;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function getVendor()
    {
        if (null !== $this->vendor) {
            return $this->vendor;
        }

        if ($vendor = $this->registry->registry('current_omnyfy_vendor_store')) {
            return $this->vendor = $vendor;
        }

        throw new NotFoundException(__('Vendor was not registered'));
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
        return $this->getVendor()->getWebsiteIds();
    }

    public function getBaseCurrencyCode()
    {
        return $this->getStore()->getBaseCurrencyCode();
    }
}
 