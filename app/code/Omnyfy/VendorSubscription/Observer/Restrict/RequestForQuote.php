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

class RequestForQuote implements \Magento\Framework\Event\ObserverInterface
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
        $requestForQuote = $observer->getData('data_object');
        $vendorId = empty($requestForQuote) ? 0 : $requestForQuote->getVendorId();

        if (!$vendorId) {
            return;
        }

        //omnyfy_rfq_show_modal, omnyfy_rfq_save_before, omnyfy_rfq_save_after, omnyfy_rfq_delete_after
        switch ($eventName) {
            case 'omnyfy_rfq_show_modal':
                $vendorId = $observer->getData('vendor_id');
                if ($this->_helper->isRunOut($vendorId, UsageType::REQUEST_FOR_QUOTE)) {
                    throw new LocalizedException(__('Vendor have used up allocation of request for quote'));
                }
                break;
            case 'omnyfy_rfq_save_before':
                //if it's update an existing page, do nothing
                if (!empty($requestForQuote) && $requestForQuote->getId()) {
                    return;
                }
                //check usage
                if ($this->_helper->isRunOut($vendorId, UsageType::REQUEST_FOR_QUOTE)) {
                    throw new LocalizedException(__('Vendor have used up allocation of request for quote.'));
                }
                break;
            case 'omnyfy_rfq_save_after':
                //save usage log
                $this->_helper->logUsage($vendorId, UsageType::REQUEST_FOR_QUOTE, $requestForQuote->getId());
                break;
            case 'omnyfy_rfq_delete_after':
                //delete usage log, update usage
                $this->_helper->returnUsageCount($vendorId, UsageType::REQUEST_FOR_QUOTE, $requestForQuote->getId());
                break;
        }
    }
}
 