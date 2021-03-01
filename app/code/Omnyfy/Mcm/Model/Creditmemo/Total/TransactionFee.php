<?php

namespace Omnyfy\Mcm\Model\Creditmemo\Total;

use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Omnyfy\Mcm\Helper\Data as FeeHelper;

/**
 * Class Fee
 * @package Omnyfy\Mcm\Model\Creditmemo\Total
 */
class TransactionFee extends AbstractTotal
{
    /**
     * @var FeeHelper
     */
    protected $_helper;

    /**
     * Fee constructor.
     *
     * @param FeeHelper $helper
     * @param array $data
     */
    public function __construct(FeeHelper $helper, array $data = [])
    {
        parent::__construct($data);
        $this->_helper = $helper;
    }

    /**
     * @param Creditmemo $creditmemo
     * @return $this
     */
    public function collect(Creditmemo $creditmemo)
    {
        $creditmemo->setMcmTransactionFee(0);
        $creditmemo->setMcmBaseTransactionFee(0);
        if (!($this->_helper->isRefundCommercialsManagementEnable() && $this->_helper->isEnable() && $this->_helper->isTransactionFeeEnable())) {
            return $this;
        }

        $amount = $creditmemo->getOrder()->getMcmTransactionFee();
        $creditmemo->setMcmTransactionFee($amount);
		
		$creditmemo->setMcmTransactionFeeTax(0);
		$creditmemo->setMcmTransactionFeeInclTax(0);

		$transactionFeeTax = $creditmemo->getOrder()->getMcmTransactionFeeTax();
        $creditmemo->setMcmTransactionFeeTax($transactionFeeTax);
		$transactionFeeInclTax = $creditmemo->getOrder()->getMcmTransactionFeeInclTax();
        $creditmemo->setMcmTransactionFeeInclTax($transactionFeeInclTax);
				
		$baseAmount = $creditmemo->getOrder()->getMcmBaseTransactionFee();
        $creditmemo->setMcmBaseTransactionFee($baseAmount);
		
		$baseTransactionFeeInclTax = $creditmemo->getOrder()->getMcmBaseTransactionFeeInclTax();
        $creditmemo->setMcmBaseTransactionFeeInclTax($baseTransactionFeeInclTax);
        
		if ($this->_helper->chargeTransactionFeeForRefundCEnable()) {
            $creditmemo->setGrandTotal(($creditmemo->getGrandTotal()) - $transactionFeeTax);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal());
        } else {
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() + $transactionFeeInclTax);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() + $baseTransactionFeeInclTax);
        }
        return $this;
    }
}
