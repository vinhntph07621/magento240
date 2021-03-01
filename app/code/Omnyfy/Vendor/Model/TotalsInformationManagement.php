<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-06-27
 * Time: 11:24
 */
namespace Omnyfy\Vendor\Model;

class TotalsInformationManagement implements \Magento\Checkout\Api\TotalsInformationManagementInterface
{
    /**
     * Cart total repository.
     *
     * @var \Magento\Quote\Api\CartTotalRepositoryInterface
     */
    protected $cartTotalRepository;

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    protected $helper;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalRepository
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalRepository,
        \Omnyfy\Vendor\Helper\Data $helper
    ) {
        $this->cartRepository = $cartRepository;
        $this->cartTotalRepository = $cartTotalRepository;
        $this->helper = $helper;
    }

    /**
     * {@inheritDoc}
     */
    public function calculate(
        $cartId,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->cartRepository->get($cartId);
        $this->validateQuote($quote);

        if ($quote->getIsVirtual()) {
            $quote->setBillingAddress($addressInformation->getAddress());
        } else {
            $quote->setShippingAddress($addressInformation->getAddress());
            //TODO: if no carrier_code or method_code set in addressInformation, leave shipping method as
            $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
            if (!empty($addressInformation->getShippingCarrierCode()) || !empty($addressInformation->getShippingMethodCode())) {
                $shippingMethod = $this->helper->parseCodeToShippingMethodString(
                    $addressInformation->getShippingCarrierCode(),
                    $addressInformation->getShippingMethodCode()
                );
            }
            $quote->getShippingAddress()->setCollectShippingRates(true)
                ->setShippingMethod($shippingMethod)
            ;
        }
        $quote->collectTotals();

        //2019-12-11 12:01 by Jing Xiao
        //Only save quote when quote is virtual, since ShippingInformationManagement will save real quote
        if ($quote->isVirtual()) {
            $this->cartRepository->save($quote);
        }

        return $this->cartTotalRepository->get($cartId);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function validateQuote(\Magento\Quote\Model\Quote $quote)
    {
        if ($quote->getItemsCount() === 0) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Totals calculation is not applicable to empty cart')
            );
        }
    }
}
 