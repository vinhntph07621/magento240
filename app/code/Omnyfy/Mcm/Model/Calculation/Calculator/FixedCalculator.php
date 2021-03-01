<?php

namespace Omnyfy\Mcm\Model\Calculation\Calculator;

use Magento\Quote\Model\Quote;
use Omnyfy\Mcm\Helper\Data as FeeHelper;

class FixedCalculator extends AbstractCalculator {

    /**
     * {@inheritdoc}
     */
    public function calculate(Quote $quote) {
        if ($this->_helper->isTransactionFeeEnable() && $this->_helper->isEnable()) {
            //$shippingTaxAmount = $add->getShippingTaxAmount();
            //$shippingDiscountAmount = $add->getShippingDiscountAmount();
            $subTotal = $quote->getSubtotal();
            $tax = 0;
            $discountAmount = 0;
            $shippingAmount = 0;
            foreach($quote->getAllAddresses() as $address) {
                $tax += $address->getTaxAmount();
                $discountAmount += $address->getDiscountAmount();
                $shippingAmount += $address->getShippingAmount();
            }
            $amount = $subTotal + $shippingAmount + $tax + $discountAmount ;
            $fee = ($amount * $this->_helper->getTransactionFeePercentage()) * 0.01 ;
            $fee_per_order = $amount > 0 ? $this->_helper->getTransactionFeeAmount() : 0;
            $transaction_fee_surcharge = ($amount * $this->_helper->getTransactionFeeSurchargePercentage()) * 0.01;
            return $fee + $fee_per_order + $transaction_fee_surcharge;
        }
    }

}
