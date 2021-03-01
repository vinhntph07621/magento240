<?php

namespace Omnyfy\Mcm\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;
use Omnyfy\Mcm\Helper\Data as FeeHelper;
use Omnyfy\Mcm\Model\Calculation\Calculator\CalculatorInterface;

/**
 * Class TransactionFeeConfigProvider
 * @package Omnyfy\Mcm\Model
 */
class TransactionFeeConfigProvider implements ConfigProviderInterface
{
    /**
     * @var FeeHelper
     */
    protected $helper;

    /**
     * @var Session
     */
    protected $checkoutSession;

    /**
     * @var CalculatorInterface
     */
    protected $calculator;

    /**
     * @param FeeHelper $helper
     * @param Session $checkoutSession
     * @param CalculatorInterface $calculator
     */
    public function __construct(FeeHelper $helper, Session $checkoutSession, CalculatorInterface $calculator) {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->calculator = $calculator;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $transactionFeeConfig = [];
        $quote = $this->checkoutSession->getQuote();
        $transactionfee = $this->calculator->calculate($quote);
		$moduleenabled = $this->helper->isEnable();
        $transactionFeeEnabled = $this->helper->isTransactionFeeEnable();
		$transactionfeeIncltax = 0;
		if ($moduleenabled && $transactionFeeEnabled) {
            $taxRate = $this->helper->getTaxRate();
            $transactionFeeTax = $transactionfee * $taxRate * 0.01;
			$transactionfeeIncltax = $transactionfee + $transactionFeeTax;
        }
		$transactionFeeConfig['transaction_fee_amount'] = $transactionfeeIncltax;
        $transactionFeeConfig['show_hide_transactionfee'] = ($moduleenabled && $transactionFeeEnabled && $quote->getMcmTransactionFee()) ? true : false;
        return $transactionFeeConfig;
    }
}
