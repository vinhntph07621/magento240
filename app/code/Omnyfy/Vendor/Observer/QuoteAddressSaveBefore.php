<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 24/2/18
 * Time: 3:14 AM
 */
namespace Omnyfy\Vendor\Observer;

use Magento\Framework\Event\ObserverInterface;

class QuoteAddressSaveBefore implements ObserverInterface
{
    protected $vendorHelper;

    public function __construct(
        \Omnyfy\Vendor\Helper\Data $helper
    )
    {
        $this->vendorHelper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $address = $observer->getData('quote_address');

        $shippingMethod = $address->getShippingMethod();

        if (is_array($shippingMethod)) {
            $address->setShippingMethod($this->vendorHelper->shippingMethodArrayToString($shippingMethod));
        }
    }
}