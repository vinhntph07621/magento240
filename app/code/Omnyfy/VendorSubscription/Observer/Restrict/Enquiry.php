<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 20/9/19
 * Time: 10:53 am
 */
namespace Omnyfy\VendorSubscription\Observer\Restrict;

use Magento\Framework\Exception\LocalizedException;
use Omnyfy\VendorSubscription\Model\Source\UsageType;

class Enquiry implements \Magento\Framework\Event\ObserverInterface
{
    protected $_helper;

    public function __construct(
        \Omnyfy\VendorSubscription\Helper\Usage $_helper
    ) {
        $this->_helper = $_helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $eventName = $observer->getEvent()->getName();
        $enquiry = $observer->getData('data_object');
        $vendorId = empty($enquiry) ? 0 : $enquiry->getVendorId();

        //omnyfy_enquiry_form_is_enabled, omnyfy_enquiry_enquiries_save_before, omnyfy_enquiry_enquiries_save_after, omnyfy_enquiry_enquiries_delete_after
        switch ($eventName) {
            case 'omnyfy_enquiry_form_is_enabled':
                $vendorId = $observer->getData('vendor_id');
                if ($this->_helper->isRunOut($vendorId, UsageType::ENQUIRY)) {
                    throw new LocalizedException(__('Vendor have used up allocation of enquiries'));
                }
                break;
            case 'omnyfy_enquiry_enquiries_save_before':
                //if it's update an existing page, do nothing
                if (!empty($enquiry) && $enquiry->getId()) {
                    return;
                }
                //check usage
                if ($this->_helper->isRunOut($vendorId, UsageType::ENQUIRY)) {
                    throw new LocalizedException(__('Vendor have used up allocation of enquiries.'));
                }
                break;
            case 'omnyfy_enquiry_enquiries_save_after':
                //save usage log
                $this->_helper->logUsage($vendorId, UsageType::ENQUIRY, $enquiry->getId());
                break;
            case 'omnyfy_enquiry_enquiries_delete_after':
                //delete usage log, update usage
                $this->_helper->returnUsageCount($vendorId, UsageType::ENQUIRY, $enquiry->getId());
                break;
        }
    }
}