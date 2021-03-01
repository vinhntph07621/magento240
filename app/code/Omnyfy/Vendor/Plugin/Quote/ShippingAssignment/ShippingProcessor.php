<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 8/2/18
 * Time: 9:31 PM
 */
namespace Omnyfy\Vendor\Plugin\Quote\ShippingAssignment;

class ShippingProcessor
{
    protected $vendorHelper;

    protected $shippingAddressManagement;

    protected $shippingMethodManagement;

    public function __construct(
        \Omnyfy\Vendor\Helper\Data $vendorHelper,
        \Magento\Quote\Model\ShippingAddressManagement $shippingAddressManagement,
        \Magento\Quote\Model\ShippingMethodManagement $shippingMethodManagement
    )
    {
        $this->vendorHelper = $vendorHelper;
        $this->shippingAddressManagement = $shippingAddressManagement;
        $this->shippingMethodManagement = $shippingMethodManagement;
    }

    public function aroundSave(
        $subject,
        callable $process,
        \Magento\Quote\Api\Data\ShippingInterface $shipping,
        \Magento\Quote\Api\Data\CartInterface $quote
    )
    {
        $method = $shipping->getMethod();
        if (empty($method) || '{' !== substr($method, 0, 1)) {
            return $process($shipping, $quote);
        }

        //To avoid issue with multi shipping
        if (!$quote->getIsMultiShipping()) {
            $this->shippingAddressManagement->assign($quote->getId(), $shipping->getAddress());
        }

        if (!empty($shipping->getMethod()) && $quote->getItemsCount() > 0) {
            $carrierCode = $this->vendorHelper->getCarrierCode($method);
            $methodCode = $this->vendorHelper->getMethodCode($method);
            $this->shippingMethodManagement->apply($quote->getId(), $carrierCode, $methodCode);
        }
    }
}