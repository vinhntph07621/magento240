<?php

namespace Omnyfy\Mcm\Block\Sales;

/**
 * Class Totals
 * @package Omnyfy\Mcm\Block\Sales
 */
class Totals extends \Magento\Framework\View\Element\Template {

    /**
     * @var \Omnyfy\Mcm\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Omnyfy\Mcm\Helper\Data $helper
     * @param \Magento\Directory\Model\Currency $currency
     * @param array $data
     */
    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Omnyfy\Mcm\Helper\Data $helper, \Magento\Directory\Model\Currency $currency, array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helper = $helper;
        $this->_currency = $currency;
    }

    public function getOrder() {
        return $this->getParentBlock()->getOrder();
    }

    public function getCurrencySymbol() {
        return $this->_currency->getCurrencySymbol();
    }

    public function initTotals() {
        $this->getParentBlock();
        $order = $this->getOrder();

        if ($this->_helper->isTransactionFeeEnable() && $this->_helper->isEnable()) {

            $total = new \Magento\Framework\DataObject(
                    [
                'code' => 'mcm_transaction_fee',
                'value' => $order->getMcmTransactionFeeInclTax(),
                'base_value' => $order->getMcmBaseTransactionFeeInclTax(),
                'label' => 'Transaction Fee',
                    ]
            );
            $this->getParentBlock()->addTotalBefore($total, 'grand_total');

            return $this;
        }
    }
}