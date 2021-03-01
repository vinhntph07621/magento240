<?php

namespace Omnyfy\Mcm\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class AfterOrder
 * @package Omnyfy\Mcm\Observer
 */
class VendorInvoice implements ObserverInterface {
    /**
     * @var \Omnyfy\Mcm\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Omnyfy\Mcm\Model\ResourceModel\FeesManagement
     */
    protected $feesManagementResource;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;

    /**
     * @param \Omnyfy\Mcm\Helper\Data 
     * @param \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        \Omnyfy\Mcm\Helper\Data $helper,
        \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource,
        DateTime $date) {
        $this->_helper = $helper;
        $this->feesManagementResource = $feesManagementResource;
        $this->_date = $date;
    }

    /**
     * Set payment fee to order
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer) {
        $invoice = $observer->getData('invoice');
        $invoiceId = $invoice->getId();
        $orderId = $invoice->getOrderId();
        if (empty($invoiceId)) {
            return;
        }

        //Only process for new invoice
        if ($invoice->getCreatedAt() !== $invoice->getUpdatedAt()) {
            return;
        }
        $items = $invoice->getAllItems();
        $vendorIds = [];
        foreach ($items as $item) {
            $vendorId = $item->getVendorId();
            if (!empty($vendorId)) {
                $vendorIds[] = $vendorId;
            }
        }
        $vendorIds = array_unique($vendorIds);

        if (empty($vendorIds)) {
            //TODO: throw exception or log errors
            return;
        }

        $data = [];
        foreach ($vendorIds as $vendorId) {
            $vendorOrderTotals = $this->feesManagementResource->getVendorOrderTotals($vendorId, $orderId);

            $data[] = [
                'invoice_id' => $invoiceId,
                'vendor_id' => $vendorId,
                'order_id' => $orderId,
                'subtotal' => $vendorOrderTotals['subtotal'],
                'base_subtotal' => $vendorOrderTotals['base_subtotal'],
                'subtotal_incl_tax' => $vendorOrderTotals['subtotal_incl_tax'],
                'base_subtotal_incl_tax' => $vendorOrderTotals['base_subtotal_incl_tax'],
                'tax_amount' => $vendorOrderTotals['tax_amount'] + $vendorOrderTotals['shipping_tax'],
                'base_tax_amount' => $vendorOrderTotals['base_tax_amount'],
                'discount_amount' => $vendorOrderTotals['discount_amount'] + $vendorOrderTotals['shipping_discount_amount'],
                'base_discount_amount' => $vendorOrderTotals['base_discount_amount'] + $vendorOrderTotals['shipping_discount_amount'],
                'shipping_amount' => $vendorOrderTotals['shipping_amount'],
                'base_shipping_amount' => $vendorOrderTotals['base_shipping_amount'],
                'shipping_incl_tax' => $vendorOrderTotals['shipping_incl_tax'],
                'base_shipping_incl_tax' => $vendorOrderTotals['base_shipping_incl_tax'],
                'shipping_tax' => $vendorOrderTotals['shipping_tax'],
                'base_shipping_tax' => $vendorOrderTotals['base_shipping_tax'],
                'shipping_discount_amount' => $vendorOrderTotals['shipping_discount_amount'],
                'grand_total' => ($vendorOrderTotals['grand_total'] + $vendorOrderTotals['shipping_amount'] + $vendorOrderTotals['shipping_tax'] - ($vendorOrderTotals['shipping_discount_amount'])),
                'base_grand_total' => ($vendorOrderTotals['base_grand_total'] + $vendorOrderTotals['shipping_amount'] + $vendorOrderTotals['shipping_tax'] - ($vendorOrderTotals['shipping_discount_amount'])),
                'created_at' => $this->_date->gmtDate(),
                'updated_at' => $this->_date->gmtDate(),
            ];
        }
        $this->feesManagementResource->saveVendorInvoiceTotals($data);

        return $this;
    }

}
