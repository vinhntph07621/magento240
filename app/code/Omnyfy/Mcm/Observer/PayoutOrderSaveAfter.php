<?php

namespace Omnyfy\Mcm\Observer;

use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer;
use Omnyfy\Mcm\Model\ResourceModel\VendorOrder\CollectionFactory as VendorOrderCollectionFactory;
use Magento\Sales\Api\OrderItemRepositoryInterface;

class PayoutOrderSaveAfter implements ObserverInterface {

    protected $objectManager;
    protected $enableMoveToPayout;
    protected $orderState;
    protected $readyToPayoutId = 1;

    const STATUS_COMPLETE = 'complete';
    const STATUS_SHIPPED = 'Shipped';
    const STATUS_INVOICED = 'Invoiced';

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    protected $vendorOrderResource;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Sales\Api\Data\OrderInterface $order,
        VendorOrderCollectionFactory $vendorOrderCollectionFactory,
        OrderItemRepositoryInterface $orderItemRepositoryInterface,
        \Omnyfy\Mcm\Model\ResourceModel\VendorOrder $vendorOrderResource
    ) {
        $this->objectManager = $objectManager;
        $this->request = $request;
        $this->_order = $order;
        $this->vendorOrderCollectionFactory = $vendorOrderCollectionFactory;
        $this->orderItemRepositoryInterface = $orderItemRepositoryInterface;
        $this->vendorOrderResource = $vendorOrderResource;
    }

    /**
     * @param $observer
     */
    public function execute(Observer $observer) {
        $shipment = $observer->getEvent()->getShipment();
        if(empty($shipment)){
            return;
        }
        $order = $shipment->getOrder();
        if (empty($order)) {
            return;
        }
        $orderAllItems = $order->getAllItems();
        if (empty($orderAllItems)) {
            return;
        }
        try {
            $this->orderState = $order->getState();
            $orderId = $order->getId();
            $vendorIds = [$shipment->getVendorId()];
            foreach ($vendorIds as $orderVendorId) {
                $vendorOrderCollection = $this->getVendorOrderCollection($orderVendorId, $orderId);
                if (!empty($vendorOrderCollection)) {
                    foreach ($vendorOrderCollection as $vendorOrder) {
                        if ($this->enableMoveToPayout($orderId, $orderVendorId)) {
                            $vendorOrder->setPayoutAction($this->readyToPayoutId);
                            $vendorOrder->save();
                        }
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e0) {
            $this->objectManager->get('Psr\Log\LoggerInterface')->critical($e0->getMessage());
        } catch (\Exception $e) {
            $this->objectManager->get('Psr\Log\LoggerInterface')->critical($e->getMessage());
        }
    }

    public function getOrderItemStatus($itemId) {
        $orderItem = $this->orderItemRepositoryInterface->get($itemId);
        $state = $orderItem->getStatus();
        return $state;
    }

    public function enableMoveToPayout($orderId, $vendorId) {
        if ($this->orderState == self::STATUS_COMPLETE) {
            $this->enableMoveToPayout = 1;
        } else {
            $itemIds = $this->vendorOrderResource->getOrderItems($orderId, $vendorId);
            if (!empty($itemIds)) {
                foreach ($itemIds as $item) {
                    $itemStatus = $this->getOrderItemStatus($item['order_item_id']);
                    if ($itemStatus == self::STATUS_SHIPPED || $itemStatus == self::STATUS_INVOICED) {
                        $this->enableMoveToPayout = 1;
                    } else {
                        $this->enableMoveToPayout = 0;
                        break;
                    }
                }
            }
        }
        return $this->enableMoveToPayout;
    }

    public function getVendorOrderCollection($vendorId, $orderId) {
        $vendorOrderCollection = $this->vendorOrderCollectionFactory->create()->addFieldToFilter('vendor_id', $vendorId)
                ->addFieldToFilter('order_id', $orderId);
        return $vendorOrderCollection;
    }

}
