<?php

namespace Omnyfy\Mcm\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class AfterOrder
 * @package Omnyfy\Mcm\Observer
 */
class AfterOrder implements ObserverInterface {
    /*
     * @var \Omnyfy\Mcm\Helper\Data
     */

    protected $_helper;

    /**
     * @param \Omnyfy\Mcm\Helper\Data 
     */
    public function __construct(\Omnyfy\Mcm\Helper\Data $helper) {
        $this->_helper = $helper;
    }

    /**
     * Set payment fee to order
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $quote = $observer->getQuote();
        $transactionFee = $quote->getMcmTransactionFee();
        $baseTransactionFee = $quote->getMcmBaseTransactionFee();
        if ((!$this->_helper->isEnable()) && (!$this->_helper->isTransactionFeeEnable()) && (!$transactionFee || !$baseTransactionFee)) {
            return $this;
        }

        $order = $observer->getOrder();
		$transaction_fee_surcharge = $this->_helper->getTransactionFeeSurchargePercentage();
		
		$transactionFeeTax = ($transactionFee * ($this->_helper->getTaxRate())) / 100;
		$baseTransactionFeeTax = $this->_helper->convertBasePrice($transactionFeeTax, $quote->getStoreId());
		
		$transactionFeeInclTax = $transactionFee + $transactionFeeTax;
        $baseTransactionFeeInclTax = $this->_helper->convertBasePrice($transactionFeeInclTax, $quote->getStoreId()); 

        $order->setData('mcm_transaction_fee', $transactionFee);
        $order->setData('mcm_base_transaction_fee', $baseTransactionFee);
        $order->setData('mcm_transaction_fee_surcharge', $transaction_fee_surcharge);
        $order->setData('mcm_transaction_fee_tax', $transactionFeeTax);
        $order->setData('mcm_base_transaction_fee_tax', $baseTransactionFeeTax);
        $order->setData('mcm_transaction_fee_incl_tax', $transactionFeeInclTax);
        $order->setData('mcm_base_transaction_fee_incl_tax', $baseTransactionFeeInclTax);
        return $this;
    }
}