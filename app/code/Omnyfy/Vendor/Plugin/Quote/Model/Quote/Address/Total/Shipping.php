<?php
/**
 * Project: Omnyfy Multi Vendor.
 * User: jing
 * Date: 5/5/17
 * Time: 4:26 PM
 */
namespace Omnyfy\Vendor\Plugin\Quote\Model\Quote\Address\Total;

use Magento\Framework\Pricing\PriceCurrencyInterface;

class Shipping
{
    protected $priceCurrency;

    protected $helper;

    protected $locationHelper;

    public function __construct(
        PriceCurrencyInterface $priceCurrency,
        \Omnyfy\Vendor\Helper\Data $helper,
        \Omnyfy\Vendor\Helper\Location $locationHelper
    )
    {
        $this->priceCurrency = $priceCurrency;
        $this->helper = $helper;
        $this->locationHelper = $locationHelper;
    }

    public function aroundCollect(
            $subject,
            callable $proceed,
            \Magento\Quote\Model\Quote $quote,
            \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
            \Magento\Quote\Model\Quote\Address\Total $total
        )
    {
        $address = $shippingAssignment->getShipping()->getAddress();
        $method = $shippingAssignment->getShipping()->getMethod();
        if ('_' === $method) {
            $method = null;
        }

        $result = $proceed($quote, $shippingAssignment, $total);

        if ($quote->getIsMultiShipping()) {
            //return $result;
        }

        $compareLocation = true;
        if (is_array($method)) {
            $methods = $method;
            $address->setShippingMethod($this->helper->shippingMethodArrayToString($method));
        }
        elseif (is_string($method)) {
            $address->setShippingMethod($method);
            if ('{' == substr($method, 0, 1)) {
                $methods = $this->helper->shippingMethodStringToArray($method);
            }
            else {
                $methods = [$method];
                $compareLocation = false;
            }
        }
        else{
            $methods = [];
        }

        if (!empty($methods)) {
            $shippingDescription = '';
            $totalAmount = $baseTotal = $shippingAmount = $baseShippingAmount = 0;
            $shippingRates =$address->getAllShippingRates();
            $data = [];
            foreach($methods as $locationId => $methodCode) {
                foreach ($shippingRates as $rate) {
                    if ((!$compareLocation || $rate->getLocationId() == $locationId) && $rate->getCode() == $methodCode) {
                        $store = $quote->getStore();
                        $amountPrice = $this->priceCurrency->convert(
                            $rate->getPrice(),
                            $store
                        );
                        $totalAmount += $amountPrice;
                        $baseTotal += $rate->getPrice();
                        $shippingAmount += $amountPrice;
                        $baseShippingAmount += $rate->getPrice();
                        $shippingDescription .= (empty($shippingDescription) ? '' : "\n") . $rate->getCarrierTitle() . ' - ' . $rate->getMethodTitle();
                        $data[] = [
                            'quote_id' => $quote->getId(),
                            'address_id' => $address->getId(),
                            'location_id' => $locationId,
                            'rate_id' => $rate->getId(),
                            'method_code' => $rate->getCode(),
                            'amount' => $amountPrice,
                            'base_amount' => $rate->getPrice(),
                            'carrier' => $rate->getCarrierTitle(),
                            'method_title' => $rate->getMethodTitle(),
                            'vendor_id' => $this->locationHelper->getVendorIdByLocationId($locationId)
                        ];
                        break;
                    }
                }
            }
            $address->setShippingDescription(trim($shippingDescription, ' -'));
            $total->setTotalAmount($subject->getCode(), $totalAmount);
            $total->setBaseTotalAmount($subject->getCode(), $baseTotal);
            $total->setBaseShippingAmount($baseShippingAmount);
            $total->setShippingAmount($shippingAmount);
            $total->setShippingDescription($shippingDescription);

            //save methods and shipping amount for quote
            $this->helper->saveQuoteShipping($quote->getId(), $data);
        }

        return $result;
    }
}