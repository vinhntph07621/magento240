<?php

namespace Omnyfy\Mcm\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Omnyfy\Mcm\Model\ShippingCalculationFactory;
use Omnyfy\Mcm\Model\ResourceModel\VendorOrder\CollectionFactory as VendorOrderCollectionFactory;

class Payout extends AbstractHelper {

    protected $_storeManager;

    protected $orderTaxManagement;

    protected $priceCurrency;

    protected $feesManagementResource;

    protected $_shippingCalculationFactory;

    protected $vendorOrderCollectionFactory;

    protected $_items = [];

    public function __construct(
        Context $context,
        \Magento\Tax\Api\OrderTaxManagementInterface $orderTaxManagement,
        StoreManagerInterface $storeManager,
        PriceCurrencyInterface $priceCurrencyInterface,
        \Omnyfy\Mcm\Model\ResourceModel\FeesManagement $feesManagementResource,
        \Omnyfy\Mcm\Model\ShippingCalculationFactory  $shippingCalculationFactory,
        VendorOrderCollectionFactory $vendorOrderCollectionFactory
    ) {
        $this->_storeManager = $storeManager;
        $this->orderTaxManagement = $orderTaxManagement;
        $this->priceCurrency = $priceCurrencyInterface;
        $this->feesManagementResource = $feesManagementResource;
        $this->_shippingCalculationFactory = $shippingCalculationFactory;
        $this->vendorOrderCollectionFactory = $vendorOrderCollectionFactory;
        parent::__construct($context);
    }

    // @TODO - placeholder to vendor payout value
    public function getVendorPayoutValue($vendorId)
    {

    }

    // @TODO - placeholder to check
    public function doesShippingCalculationExist($vendorOrder)
    {
        // Get order shipments that are ship by type 2 which is vendor
        // ship_by_type = 1 (Marketplace Owner)
        // ship_by_type = 2 (Vendor)
        $vendorShipments = $this->_shippingCalculationFactory
            ->create()
            ->getCollection()
            ->addFieldToFilter('order_id', $vendorOrder->getOrderId())
            ->addFieldToFilter('vendor_id', $vendorOrder->getVendorId())
            ->addFieldToFilter('ship_by_type', '2');

        if ($vendorShipments->getSize() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function getOrderPayoutShippingAmount($vendorOrder)
    {
        // Get order shipments that are ship by type 2 which is vendor
        // ship_by_type = 1 (Marketplace Owner)
        // ship_by_type = 2 (Vendor)
        $vendorShipments = $this->_shippingCalculationFactory
            ->create()
            ->getCollection()
            ->addFieldToFilter('order_id', $vendorOrder->getOrderId())
            ->addFieldToFilter('vendor_id', $vendorOrder->getVendorId())
            ->addFieldToFilter('ship_by_type', '2');

        $orderShipmentTotal = 0;
        if ($vendorShipments->getSize() > 0) {
            foreach($vendorShipments as $vendorShipment) {
                $orderShipmentTotal += $vendorShipment->getCustomerPaid();
            }
        }

        return $orderShipmentTotal;
    }

    public function getReadyToPayoutVendorOrderCollection($vendorId)
    {
        $vendorOrderCollection = $this->vendorOrderCollectionFactory->create();
        $vendorOrderCollection = $vendorOrderCollection->addFieldToFilter('vendor_id', $vendorId)
            ->addFieldToFilter('payout_status', 0)
            ->addFieldToFilter('payout_action', 1);

        return $vendorOrderCollection;
    }
}
