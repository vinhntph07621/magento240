<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 24/7/17
 * Time: 4:49 PM
 */

namespace Omnyfy\Vendor\Plugin\Quote\Model;

use Magento\Framework\Exception\LocalizedException;

class QuoteValidator
{

    protected $helper;

    protected $extraHelper;

    protected $shippingHelper;

    public function __construct(
        \Omnyfy\Vendor\Helper\Data $helper,
        \Omnyfy\Vendor\Helper\Extra $extraHelper,
        \Omnyfy\Vendor\Helper\Shipping $shippingHelper
    )
    {
        $this->helper = $helper;
        $this->extraHelper = $extraHelper;
        $this->shippingHelper = $shippingHelper;
    }

    public function aroundValidateBeforeSubmit(
        \Magento\Quote\Model\QuoteValidator $subject,
        callable $proceed,
        \Magento\Quote\Model\Quote $quote
    )
    {
        $errors = $this->extraHelper->getJoinedErrorMsg($quote);
        if (!empty($errors)) {
            throw new \Magento\Framework\Exception\LocalizedException(__($errors));
        }

        if (!$quote->isVirtual()) {
            if ($quote->getIsMultiShipping()) {
                $this->extraHelper->validateLocation($quote);
                $this->extraHelper->validateQuoteItems($quote);

                //Validate all shipping addresses
                $addresses = $quote->getAllShippingAddresses();
                foreach($addresses as $address) {
                    if ($address->validate() !== true) {
                        throw new \Magento\Framework\Exception\LocalizedException(
                            __('Please check the shipping address information. %1',
                                implode(' ', $address->validate())
                            )
                        );
                    }

                    if (!$this->isAddressShippingMethodSelected($address)) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('Please specify a shipping method.'));
                    }
                }
            }
            else {
                if ($quote->getShippingAddress()->validate() !== true) {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __(
                            'Please check the shipping address information. %1',
                            implode(' ', $quote->getShippingAddress()->validate())
                        )
                    );
                }
                if (! $this->isShippingMethodSelected($quote)) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('Please specify a shipping method.'));
                }
            }
        }
        if ($quote->getBillingAddress()->validate() !== true) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(
                    'Please check the billing address information. %1',
                    implode(' ', $quote->getBillingAddress()->validate())
                )
            );
        }
        if (!$quote->getPayment()->getMethod()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please select a valid payment method.'));
        }
        return $subject;
    }

    protected function isShippingMethodSelected(\Magento\Quote\Model\Quote $quote)
    {
        $shippingConfiguration = $this->shippingHelper->getCalculateShippingBy();
        if ($shippingConfiguration == 'overall_cart') {
            $shippingPickupLocation = $this->shippingHelper->getShippingConfiguration('overall_pickup_location');
        }

        $locationIds = $this->helper->getLocationIds($quote);
        $noShippingLocationIds = $this->helper->getBookingLocationIds($quote->getAllItems());

        $allRates = $quote->getShippingAddress()->getAllShippingRates();

        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();

        $shippingMethodCodes = [];

        if (is_string($shippingMethod)) {
            if ('{' == substr($shippingMethod, 0, 1)) {
                $shippingMethodCodes = $this->helper->shippingMethodStringToArray($shippingMethod);
            }
            else{
                //2016-06-28 14:26 Jing Xiao
                //set same method for all location by default
                foreach($locationIds as $locationId) {
                    $shippingMethodCodes[$locationId] = $shippingMethod;
                }
            }
        }
        elseif (is_array($shippingMethod)) {
            $shippingMethodCodes = $shippingMethod;
        }

        foreach($locationIds as $locationId) {
            // check if overall shipping is set
            if ($shippingConfiguration == 'overall_cart' && !empty($shippingPickupLocation)) {
                $locationId = $shippingPickupLocation;
            }
            if (in_array($locationId, $noShippingLocationIds)) {
                continue;
            }
            if (array_key_exists($locationId, $shippingMethodCodes)) {
                foreach($allRates as $rate) {
                    if ($rate->getLocationId() == $locationId
                        && $shippingMethodCodes[$locationId] == $rate->getCode()
                    ) {
                        continue 2;
                    }
                }
                return false;
            }
            else{
                return false;
            }
        }
        return true;
    }

    public function isAddressShippingMethodSelected($address)
    {
        $locationIds = $this->extraHelper->getAddressLocationIds($address);
        $allRates = $address->getAllShippingRates();
        $shippingMethod = $address->getShippingMethod();
        $shippingMethodCodes = [];

        if (is_string($shippingMethod)) {
            if ('{' == substr($shippingMethod, 0, 1)) {
                $shippingMethodCodes = $this->helper->shippingMethodStringToArray($shippingMethod);
            }
            else{
                //2016-06-28 14:26 Jing Xiao
                //set same method for all location by default
                foreach($locationIds as $locationId) {
                    $shippingMethodCodes[$locationId] = $shippingMethod;
                }
            }
        }
        elseif (is_array($shippingMethod)) {
            $shippingMethodCodes = $shippingMethod;
        }

        foreach($locationIds as $locationId) {
            if (array_key_exists($locationId, $shippingMethodCodes)) {
                foreach($allRates as $rate) {
                    if ($rate->getLocationId() == $locationId
                        && $shippingMethodCodes[$locationId] == $rate->getCode()
                    ) {
                        continue 2;
                    }
                }
                return false;
            }
            else{
                return false;
            }
        }
        return true;
    }
}
