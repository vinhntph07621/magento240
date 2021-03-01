<?php

namespace Omnyfy\Mcm\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Omnyfy\Mcm\Model\ResourceModel\VendorOrder\CollectionFactory as VendorOrderCollectionFactory;

class RefundEarning implements ObserverInterface {

   
    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(VendorOrderCollectionFactory $vendorOrderCollectionFactory, \Magento\Sales\Model\Order\Item $orderItem) {

        $this->vendorOrderCollectionFactory = $vendorOrderCollectionFactory;
        $this->orderItem = $orderItem;
    }

    public function execute(EventObserver $observer) {
        /* @var $creditmemo \Magento\Sales\Model\Order\Creditmemo */
        $creditmemo = $observer->getEvent()->getCreditmemo();
        $vendorIds = [];
        $orderId = $creditmemo->getOrderId();
        //$refundGrandTotal = $creditmemo->getGrandTotal();
        foreach ($creditmemo->getAllItems() as $item) {
            $itemOrdered = $this->orderItem->load($item->getOrderItemId());
            $vendorIds[] = $itemOrdered->getData('vendor_id');
        }
        $vendorIds = array_unique($vendorIds);
        if (!empty($vendorIds)) {
            foreach ($vendorIds as $vendorId) {
                $vendorOrderCollection = $this->vendorOrderCollectionFactory->create()->addFieldToFilter('vendor_id', $vendorId)
                        ->addFieldToFilter('order_id', $orderId);
                if (!empty($vendorOrderCollection)) {
                    /**
                     * payout_status = 2 and payout_action = 2 means refunded
                     */
                    foreach ($vendorOrderCollection as $vendorOrder) {
                        $vendorOrder->setPayoutStatus(2);
                        $vendorOrder->setPayoutAction(2);
                        $vendorOrder->save();
                    }
                }
            }
        }
    }

}
