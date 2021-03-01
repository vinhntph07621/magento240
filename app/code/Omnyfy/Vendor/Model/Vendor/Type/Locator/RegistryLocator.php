<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-06
 * Time: 17:05
 */
namespace Omnyfy\Vendor\Model\Vendor\Type\Locator;

use Magento\Framework\Exception\NotFoundException;

class RegistryLocator implements LocatorInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Omnyfy\Vendor\Api\Data\VendorTypeInterface
     */
    private $vendorType;

    public function __construct(\Magento\Framework\Registry $registry)
    {
        $this->registry = $registry;
    }

    public function getVendorType()
    {
        if (null !== $this->vendorType) {
            return $this->vendorType;
        }

        if ($vendorType = $this->registry->registry('current_omnyfy_vendor_vendor_type')) {
            return $this->vendorType = $vendorType;
        }

        throw new NotFoundException(__('Vendor Type was not registered'));
    }
}