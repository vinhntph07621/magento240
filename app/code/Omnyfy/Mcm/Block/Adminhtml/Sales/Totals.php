<?php

namespace Omnyfy\Mcm\Block\Adminhtml\Sales;

/**
 * Class Totals
 * @package Omnyfy\Mcm\Block\Adminhtml\Sales
 */
class Totals extends \Magento\Sales\Block\Adminhtml\Totals {

    /**
     * Associated array of totals
     * array(
     *  $totalCode => $totalObject
     * )
     *
     * @var array
     */
    protected $_totals;

    /**
     * @var \Omnyfy\Mcm\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_currency;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession;

    /**
     * @var Omnyfy\Mcm\Model\ResourceModel\FeesManagement
     */
    protected $feesManagementResource;

    protected $backendHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Omnyfy\Mcm\Helper\Data $helper
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param \Magento\Directory\Model\Currency $currency
     * @param \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource
     * @param \Omnyfy\Vendor\Helper\Backend $backendHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Omnyfy\Mcm\Helper\Data $helper,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Directory\Model\Currency $currency,
        \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource,
        \Omnyfy\Vendor\Helper\Backend $backendHelper,
        array $data = []
    ) {
        parent::__construct($context, $registry, $adminHelper);
        $this->_helper = $helper;
        $this->_adminSession = $adminSession;
        $this->_currency = $currency;
        $this->feesManagementResource = $feesManagementResource;
        $this->backendHelper = $backendHelper;
    }

    public function getOrder() {
        return $this->getParentBlock()->getOrder();
    }

    public function getCurrencySymbol() {
        return $this->_currency->getCurrencySymbol();
    }

    /**
     * Format total value based on order currency
     *
     * @param \Magento\Framework\DataObject $total
     * @return string
     */
    public function formatValue($total)
    {
        if (!$total->getIsFormated()) {
            return $this->_adminHelper->displayPrices($this->getOrder(), $total->getBaseValue(), $total->getValue());
        }
        return $total->getValue();
    }
    
    public function initTotals() {
        //$roleData = $this->_adminSession->getUser()->getRole()->getData();
        $userData = $this->_adminSession->getUser()->getData();

        $this->getParentBlock();
        $dataObject = $this->getOrder();
        $order = false;
        if ($dataObject instanceof \Magento\Sales\Model\Order) {
            $order = $dataObject;
        } else {
            $order = $dataObject->getOrder();
        }
        $orderId = $order->getEntityId();

        if ($this->backendHelper->isVendor()) {
            $userId = $userData['user_id'];
            $vendorId = $this->feesManagementResource->getVendorByUserId($userId);
            $vendorOrderTotals = $this->feesManagementResource->getVendorOrderTotals($vendorId, $orderId);

            $total = new \Magento\Framework\DataObject(
                    [
                'code' => 'subtotal',
                'value' => $vendorOrderTotals['subtotal'],
                'label' => 'Subtotal',
                    ]
            );
            $this->getParentBlock()->addTotalBefore($total, 'shipping');

            $total = new \Magento\Framework\DataObject(
                    [
                'code' => 'shipping',
                'value' => $vendorOrderTotals['shipping_amount'],
                'label' => 'Shipping & Handling',
                    ]
            );
            $this->getParentBlock()->addTotalBefore($total, 'grand_total');
            if ($vendorOrderTotals['discount_amount'] != 0.00) {
                $total = new \Magento\Framework\DataObject(
                        [
                    'code' => 'vendor_discount',
                    'value' => $vendorOrderTotals['discount_amount'] + $vendorOrderTotals['shipping_discount_amount'],
                    'label' => 'Discount',
                        ]
                );
                $this->getParentBlock()->addTotalBefore($total, 'grand_total');
            }
            $total = new \Magento\Framework\DataObject(
                    [
                'code' => 'vendor_tax',
                'value' => $vendorOrderTotals['tax_amount'] + $vendorOrderTotals['shipping_tax'],
                'label' => 'Tax',
                    ]
            );
            $this->getParentBlock()->addTotalBefore($total, 'grand_total');

            $total = new \Magento\Framework\DataObject(
                    [
                'code' => 'grand_total',
                'value' => $vendorOrderTotals['grand_total'] + $vendorOrderTotals['shipping_amount'] + $vendorOrderTotals['shipping_tax'] - $vendorOrderTotals['shipping_discount_amount'],
                'label' => 'Grand Total',
                'area' => 'footer',
                    ]
            );
            $this->getParentBlock()->addTotalBefore($total, 'paid');

            /* #[PROM-113] been asked to remove paid, refunded, and due for vendors
            $total = new \Magento\Framework\DataObject(
                    [
                'code' => 'paid',
                'value' => 0,
                'label' => 'Total Paid',
                'area' => 'footer',
                    ]
            );
            $this->getParentBlock()->addTotalBefore($total, 'refunded');

            $total = new \Magento\Framework\DataObject(
                    [
                'code' => 'refunded',
                'value' => 0,
                'label' => 'Total Refunded',
                'area' => 'footer',
                    ]
            );
            $this->getParentBlock()->addTotalBefore($total, 'due');

            $total = new \Magento\Framework\DataObject(
                    [
                'code' => 'vendor_due',
                'value' => $vendorOrderTotals['grand_total'] + $vendorOrderTotals['shipping_amount'] + $vendorOrderTotals['shipping_tax'] - $vendorOrderTotals['shipping_discount_amount'],
                'label' => 'Total Due',
                'area' => 'footer',
                    ]
            );
            $this->getParentBlock()->addTotalBefore($total, 'due');
            */
            $this->getParentBlock()->removeTotal('paid');
            $this->getParentBlock()->removeTotal('refunded');

            $this->getParentBlock()->removeTotal('due');
            $this->getParentBlock()->removeTotal('tax');
            $this->getParentBlock()->removeTotal('discount');
        } else {
            $mcmFee = $order->getMcmTransactionFee();
            if (empty($mcmFee)) {
                return $this;
            }
            $total = new \Magento\Framework\DataObject(
                    [
                'code' => 'mcm_transaction_fee',
                'value' => $order->getMcmTransactionFeeInclTax(),
                'base_value' => $order->getMcmBaseTransactionFeeInclTax(),
                'label' => 'Transaction Fee (incl. tax)',
                    ]
            );
            $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        }
        return $this;
    }
}