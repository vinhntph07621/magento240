<?php

namespace Omnyfy\Mcm\Model;

use Magento\Framework\Model\AbstractModel;
use Omnyfy\Mcm\Model\ResourceModel\VendorPayoutInvoice\VendorPayoutInvoiceOrder\Collection as InvoiceOrderCollection;

class VendorPayoutInvoice extends AbstractModel {

    /**
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     */
    public function __construct(
    \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, InvoiceOrderCollection $invoiceOrderCollection, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = []
    ) {
        $this->invoiceOrderCollection = $invoiceOrderCollection;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Define resource model
     */
    protected function _construct() {
        $this->_init('Omnyfy\Mcm\Model\ResourceModel\VendorPayoutInvoice');
    }

    public function getCollectionItemById() {
        return $this->getCollection()->addFieldToFilter('main_table.id', $this->getId())->getFirstItem();
    }

    public function getVendorName() {
        if (!$this->hasData('vendor_name')) {
            $this->setData('vendor_name', $this->getCollectionItemById()->getVendorName());
        }
        return $this->getData('vendor_name');
    }

    public function getVendorAddress() {
        if (!$this->hasData('vendor_address')) {
            $this->setData('vendor_address', $this->getCollectionItemById()->getVendorAddress());
        }
        return $this->getData('vendor_address');
    }

    public function getVendorPhone() {
        if (!$this->hasData('vendor_phone')) {
            $this->setData('vendor_phone', $this->getCollectionItemById()->getVendorPhone());
        }
        return $this->getData('vendor_phone');
    }
    
    public function getVendorAbn() {
        if (!$this->hasData('vendor_abn')) {
            $this->setData('vendor_abn', $this->getCollectionItemById()->getVendorAbn());
        }
        return $this->getData('vendor_abn');
    }
    
    public function getAllInvoiceOrders() {
        if (!$this->hasData('all_invoice_orders')) {
            $this->setData('all_invoice_orders', $this->invoiceOrderCollection->addFieldToFilter('invoice_id', $this->getId())->getItems());
        }
        return $this->getData('all_invoice_orders');
    }

}
