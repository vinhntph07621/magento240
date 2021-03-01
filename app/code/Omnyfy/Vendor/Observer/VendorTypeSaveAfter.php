<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-07-28
 * Time: 00:33
 */
namespace Omnyfy\Vendor\Observer;

use Magento\Framework\Event\ObserverInterface;

class VendorTypeSaveAfter implements ObserverInterface
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $vendorType = $observer->getData('data_object');

        if (!empty($vendorType) && $vendorType->getId()) {
            $origVendorSetId = $vendorType->getOrigData('vendor_attribute_set_id');
            $origLocationSetId = $vendorType->getOrigData('location_attribute_set_id');

            if ($origVendorSetId == $vendorType->getVendorAttributeSetId()
                && $origLocationSetId == $vendorType->getLocationAttributeSetId()) {
                return;
            }

            $vendorType->getResource()->updateAttributeSetId(
                $vendorType->getId(),
                $vendorType->getVendorAttributeSetId(),
                $vendorType->getLocationAttributeSetId()
            );
        }
    }
}
 