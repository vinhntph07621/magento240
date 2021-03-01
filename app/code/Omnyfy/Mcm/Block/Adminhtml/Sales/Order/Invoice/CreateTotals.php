<?php

namespace Omnyfy\Mcm\Block\Adminhtml\Sales\Order\Invoice;

class CreateTotals extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Omnyfy\Mcm\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Sales\Model\Order\Invoice
     */
    protected $_invoice = null;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $_source;
    
    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession;
    
    /**
     * @var \Omnyfy\Mcm\Model\ResourceModel\FeesManagement
     */
    protected $feesManagementResource;

    /**
     * @var \Omnyfy\Vendor\Helper\Backend
     */
    protected $_backendHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Omnyfy\Mcm\Helper\Data $helper
     * @param \Magento\Backend\Model\Auth\Session $adminSession
     * @param \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource
     * @param \Omnyfy\Vendor\Helper\Backend $backendHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Omnyfy\Mcm\Helper\Data $helper,
        \Magento\Backend\Model\Auth\Session $adminSession,    
        \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource,
        \Omnyfy\Vendor\Helper\Backend $backendHelper,
        array $data = []
    ) {
        $this->_helper = $helper;
        $this->_adminSession = $adminSession;
        $this->feesManagementResource = $feesManagementResource;
        $this->_backendHelper = $backendHelper;
        parent::__construct($context, $data);
    }

    public function getInvoice()
    {
        return $this->getParentBlock()->getInvoice();
    }

    public function initTotals()
    {
        $userData = $this->_adminSession->getUser()->getData();
        
        $this->getParentBlock();
        $invoice = $this->getInvoice();

        if ($this->_backendHelper->isVendor()) {
            $userId = $userData['user_id'];
            $vendorId = $this->feesManagementResource->getVendorByUserId($userId);

            $invoiceId = $invoice->getId();
            $orderId = $invoice->getOrderId();
            $vendorInvoiceTotals = $this->feesManagementResource->getVendorOrderTotals($vendorId, $orderId);
            
        $total = new \Magento\Framework\DataObject(
                    [
                'code' => 'subtotal',
                'value' => $vendorInvoiceTotals['subtotal'],
                'label' => 'Subtotal',
                    ]
            );
        $this->getParentBlock()->addTotalBefore($total, 'shipping');
            
        
        $total = new \Magento\Framework\DataObject(
                    [
                'code' => 'shipping',
                'value' => $vendorInvoiceTotals['shipping_amount'],
                'label' => 'Shipping & Handling',
                    ]
            );
            $this->getParentBlock()->addTotalBefore($total, 'grand_total');
            
        if($vendorInvoiceTotals['discount_amount'] != 0.00){
         $total = new \Magento\Framework\DataObject(
                    [
                'code' => 'vendor_discount',
                'value' => $vendorInvoiceTotals['discount_amount'],
                'label' => 'Discount',
                    ]
            );
            $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        }    
        $total = new \Magento\Framework\DataObject(
                    [
                'code' => 'vendor_tax',
                'value' => $vendorInvoiceTotals['tax_amount'] + $vendorInvoiceTotals['shipping_tax'],
                'label' => 'Tax',
                    ]
            );
        $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        
        $total = new \Magento\Framework\DataObject(
                    [
                'code' => 'vendor_grand_total',       
                'value' => $vendorInvoiceTotals['grand_total'] + $vendorInvoiceTotals['shipping_amount'] + $vendorInvoiceTotals['shipping_tax'] - $vendorInvoiceTotals['shipping_discount_amount'],
                'label' => 'Grand Total',
                'area' => 'footer',        
                    ]
            );
        $this->getParentBlock()->addTotalBefore($total, 'paid');

        $this->getParentBlock()->removeTotal('tax');
        $this->getParentBlock()->removeTotal('grand_total');
        $this->getParentBlock()->removeTotal('discount');
        }else{
            if(empty($invoice->getMcmTransactionFee()) || $invoice->getMcmTransactionFee() < 0.001) {
                return $this;
            }

            $total = new \Magento\Framework\DataObject(
                [
                    'code' => 'mcm_transaction_fee',
                    'value' => $invoice->getMcmTransactionFeeInclTax(),
                    'label' => 'Transaction Fee (incl. tax)',
                ]
            );
    
            $this->getParentBlock()->addTotalBefore($total, 'grand_total');
        }
        return $this;
    }
}
