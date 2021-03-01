<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 8/2/18
 * Time: 11:43 PM
 */
namespace Omnyfy\Vendor\Plugin;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;

class ShippingMethodManagement
{
    protected $quoteRepository;

    protected $vendorHelper;

    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Omnyfy\Vendor\Helper\Data $vendorHelper
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->vendorHelper = $vendorHelper;
    }

    public function aroundApply($subject, callable $process, $cartId, $carrierCode, $methodCode)
    {
        if ('{' !== substr($carrierCode, 0, 1)) {
            $process($cartId, $carrierCode, $methodCode);
            return;
        }

        $quote = $this->quoteRepository->getActive($cartId);
        if (0 == $quote->getItemsCount()) {
            throw new InputException(__('Shipping method is not applicable for empty cart'));
        }
        if ($quote->isVirtual()) {
            throw new NoSuchEntityException(
                __('Cart contains virtual product(s) only. Shipping method is not applicable.')
            );
        }
        $shippingAddress = $quote->getShippingAddress();
        if (!$shippingAddress->getCountryId()) {
            throw new StateException(__('Shipping address is not set'));
        }

        $shippingAddress->setShippingMethod(
            $this->vendorHelper->parseCodeToShippingMethodString($carrierCode, $methodCode)
        );
    }
}