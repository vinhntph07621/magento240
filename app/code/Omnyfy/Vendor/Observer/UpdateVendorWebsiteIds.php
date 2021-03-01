<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-08-06
 * Time: 17:48
 */
namespace Omnyfy\Vendor\Observer;

class UpdateVendorWebsiteIds implements \Magento\Framework\Event\ObserverInterface
{
    protected $_helper;

    public function __construct(
        \Omnyfy\Vendor\Helper\Backend $helper
    )
    {
        $this->_helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $vendorId = $observer->getData('vendor_id');
        $websiteIds = $observer->getData('website_ids');
        $websiteIds = empty($websiteIds) ? [] : $websiteIds;

        if (empty($vendorId)) {
            return;
        }

        $this->_helper->updateWebsiteIds(
            $vendorId,
            $this->_helper->getWebsiteIdsByVendorId($vendorId),
            $websiteIds
        );
    }
}
 