<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 7/2/18
 * Time: 5:22 PM
 */
namespace Omnyfy\Vendor\Plugin;

use Omnyfy\Vendor\Helper\Data as  VendorHelper;

class PaymentInformationManagement
{
    private $cartRepository;

    protected $vendorHelper;

    protected $paymentMethodManagement;

    public function __construct(
        VendorHelper $vendorHelper,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository = null
    )
    {
        $this->vendorHelper = $vendorHelper;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->cartRepository = $cartRepository ? : \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\Quote\Api\CartRepositoryInterface::class);
    }

    public function aroundSavePaymentInformation(
        $subject,
        callable $proceed,
        $cartId,
        \Magento\Quote\Api\Data\PaymentInterface $paymentMethod,
        \Magento\Quote\Api\Data\AddressInterface $billingAddress = null
    )
    {
        if (empty($billingAddress)) {
            return $proceed($cartId, $paymentMethod, $billingAddress);
        }

        if ($billingAddress) {
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $this->cartRepository->getActive($cartId);
            //$quote->removeAddress($quote->getBillingAddress()->getId());
            $quote->setBillingAddress($billingAddress);
            $quote->setDataChanges(true);
            $shippingAddress = $quote->getShippingAddress();
            if ($shippingAddress && $shippingAddress->getShippingMethod()) {
                $shippingCarrier = $this->vendorHelper->getLimitCarrier($shippingAddress->getShippingMethod());
                $shippingAddress->setLimitCarrier($shippingCarrier);
            }
        }
        $this->paymentMethodManagement->set($cartId, $paymentMethod);

        return true;
    }
}