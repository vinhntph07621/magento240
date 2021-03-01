<?php

namespace Omnyfy\Mcm\Block\Adminhtml\Sales\Order\Creditmemo;

class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Sales\Model\Order\Creditmemo
     */
    protected $_creditmemo = null;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;

    /**
     * @var \Omnyfy\Mcm\Helper\Data
     */
    protected $_helper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Omnyfy\Mcm\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Omnyfy\Mcm\Helper\Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    /**
     * @return \Magento\Framework\DataObject
     */

    public function getCreditmemo()
    {
        return $this->getParentBlock()->getCreditmemo();
    }

    public function initTotals()
    {
        $this->getParentBlock();
        $creditMemo = $this->getCreditmemo();

        $mcmFee = $creditMemo->getMcmTransactionFee();
        if (empty($mcmFee)) {
            return $this;
        }

        $transactionFee = new \Magento\Framework\DataObject(
            [
                'code' => 'mcm_transaction_fee',
                'strong' => false,
                'value' => $creditMemo->getMcmTransactionFeeInclTax(),
                'label' => 'Transaction Fee (incl. tax)',
            ]
        );

        $this->getParentBlock()->addTotalBefore($transactionFee, 'grand_total');

        return $this;
    }
}
