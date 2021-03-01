<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Order\Create\Shipping\Method;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Adminhtml sales order create shipping method form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Form extends \Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form
{
    protected $vendorHelper;

    public function __construct(
        \Omnyfy\Vendor\Helper\Data $vendorHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Tax\Helper\Data $taxData,
        array $data = [])
    {
        $this->vendorHelper = $vendorHelper;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $taxData, $data);
    }

    public function getShippingRates()
    {
        if (empty($this->_rates)) {
            $rates = $this->getAddress()->getAllShippingRates();
            $result = [];
            foreach($rates as $rate) {
                $locationId = $rate->getLocationId();
                $carrierCode = $rate->getCarrier();
                if (!array_key_exists($locationId, $result)) {
                    $result[$locationId] = [];
                }
                if (!array_key_exists($carrierCode, $result[$locationId])){
                    $result[$locationId][$carrierCode] = [];
                }
                $result[$locationId][$carrierCode][] = $rate;
            }
            $this->_rates = $result;
        }
        return $this->_rates;
    }

    public function isMethodActiveInLocation($code, $locationId) {
        $methodString = $this->getShippingMethod();

        if ('{' == substr($methodString, 0, 1)) {
            $shippingMethod = $this->vendorHelper->shippingMethodStringToArray($methodString);

            return isset($shippingMethod[$locationId]) && $code == $shippingMethod[$locationId];
        }

        return $code === $methodString;
    }

    public function getActiveMethodRate()
    {
        $shippingMethodStr = $this->getShippingMethod();
        $result = [];
        $rates = $this->getShippingRates();

        if (is_array($rates)) {
            if ('{' == substr($shippingMethodStr, 0, 1)) {
                $shippingMethod = $this->vendorHelper->shippingMethodStringToArray($this->getShippingMethod());

                foreach ($rates as $locationId => $group) {
                    foreach ($group as $cCode => $rateArr) {
                        foreach ($rateArr as $rate) {
                            $code = $rate->getCode();
                            if (isset($shippingMethod[$locationId]) && $code == $shippingMethod[$locationId]) {
                                $result[$locationId] = $rate;
                                break 2;
                            }
                        }

                    }
                }

            }
            else {
                foreach($rates as $locationId => $group) {
                    foreach($group as $cCode => $rateArr) {
                        foreach($rateArr as $rate) {
                            $code = $rate->getCode();
                            if ($code == $shippingMethodStr) {
                                $result[$locationId] = $rate;
                                break 3;
                            }
                        }
                    }
                }
            }
        }

        return empty($result) ? false : $result;
    }
}
