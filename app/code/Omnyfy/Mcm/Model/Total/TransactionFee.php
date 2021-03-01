<?php

namespace Omnyfy\Mcm\Model\Total;

use Magento\Framework\Phrase;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address;
use Omnyfy\Mcm\Helper\Data as FeeHelper;
use Omnyfy\Mcm\Model\Calculation\Calculator\CalculatorInterface;

/**
 * Class Fee
 * @package Omnyfy\Mcm\Model\Total
 */
class TransactionFee extends Address\Total\AbstractTotal {

    /**
     * @var FeeHelper
     */
    protected $_helper;

    /**
     * @var CalculatorInterface
     */
    protected $calculator;

    /**
     * @param FeeHelper $helper
     * @param CalculatorInterface $calculator
     */
    public function __construct(FeeHelper $helper, CalculatorInterface $calculator) {
        $this->calculator = $calculator;
        $this->_helper = $helper;
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Address\Total $total
     * @return $this
     */
    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Address\Total $total) {
        parent::collect($quote, $shippingAssignment, $total);

		if ($quote->isVirtual() && 'shipping' == $this->_getAddress()->getAddressType()) {
		    return $this;
        }
		elseif (!$quote->isVirtual() && 'billing' == $this->_getAddress()->getAddressType()){
		    return $this;
        }

		$transactionFee = $this->calculator->calculate($quote);
        $transactionFee = empty($transactionFee) ? 0 : $transactionFee;
        $baseTransactionFee = $this->_helper->convertBasePrice($transactionFee, $quote->getStoreId());
        $total->setTotalAmount('mcm_transaction_fee', $transactionFee);
        $total->setBaseTotalAmount('mcm_transaction_fee', $baseTransactionFee);
        $total->setMcmTransactionFee($transactionFee);
        $total->setMcmBaseTransactionFee($baseTransactionFee);
        $quote->setMcmTransactionFee($transactionFee);
        $quote->setMcmBaseTransactionFee($baseTransactionFee);
        if ($this->_helper->isEnable() && $this->_helper->isTransactionFeeEnable()) {
            $taxRate = $this->_helper->getTaxRate();
            $transactionFeeTax = $transactionFee * $taxRate / 100;
        } else {
            $transactionFeeTax = 0;
        }
        $baseTransactionFeeTax = $this->_helper->convertBasePrice($transactionFeeTax, $quote->getStoreId());
        $total->setMcmTransactionFeeTax($transactionFeeTax);
        $total->setMcmTransactionFeeInclTax($transactionFeeTax + $transactionFee);
        $total->setTotalAmount('mcm_transaction_fee_tax', $transactionFeeTax);
        $total->setBaseTotalAmount('mcm_transaction_fee_tax', $baseTransactionFeeTax);
        $quote->setMcmTransactionFeeTax($transactionFeeTax);
        $quote->setMcmTransactionFeeInclTax($transactionFeeTax + $transactionFee);
        //$quote->setGrandTotal($total->getGrandTotal() + $transactionFeeTax + $transactionFee);
        //$quote->setBaseGrandTotal($total->getBaseGrandTotal() + $baseTransactionFeeTax + $baseTransactionFee);
		return $this;
    }

    /**
     * @param Address\Total $total
     */
    protected function clearValues(Address\Total $total) {
        $total->setTotalAmount('subtotal', 0);
        $total->setBaseTotalAmount('subtotal', 0);
        $total->setTotalAmount('tax', 0);
        $total->setBaseTotalAmount('tax', 0);
        $total->setTotalAmount('discount_tax_compensation', 0);
        $total->setBaseTotalAmount('discount_tax_compensation', 0);
        $total->setTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setBaseTotalAmount('shipping_discount_tax_compensation', 0);
        $total->setSubtotalInclTax(0);
        $total->setBaseSubtotalInclTax(0);
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param Quote $quote
     * @param Address\Total $total
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(Quote $quote, Address\Total $total) {
        $result = [];
        $transactionFee = $this->calculator->calculate($quote);

        if ($transactionFee > 0.0) {
            $result = [
                'code' => 'mcm_transaction_fee',
                'title' => 'Transaction Fee',
                'value' => $transactionFee,
                'base_value' => $this->_helper->convertBasePrice($transactionFee, $quote->getStoreId()),
            ];
        }

        return $result;
    }

}