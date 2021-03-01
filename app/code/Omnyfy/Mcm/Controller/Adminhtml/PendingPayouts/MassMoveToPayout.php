<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\PendingPayouts;

use Magento\Backend\App\Action\Context;
use Omnyfy\Mcm\Model\ResourceModel\VendorPayout\CollectionFactory as VendorPayoutCollectionFactory;
use Magento\Ui\Component\MassAction\Filter;
use Omnyfy\Mcm\Model\ResourceModel\VendorOrder\CollectionFactory as VendorOrderCollectionFactory;
use Omnyfy\Mcm\Model\VendorPayoutHistoryFactory;
use Omnyfy\Mcm\Model\SequenceFactory;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Omnyfy\Mcm\Model\ResourceModel\VendorOrder;
use Omnyfy\Mcm\Model\ShippingCalculationFactory;

/**
 * Class MassProcessPayouts
 */
class MassMoveToPayout extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    /**
     * @var \Magento\Ui\Component\MassAction\Filter $filter
     */
    protected $filter;

    protected $vendorOrderCollectionFactory;

    protected $orderRepository;

    protected $orderItemRepository;

    protected $vendorOrderResource;
    /**
     * @var Omnyfy\RequestQuote\Model\ResourceModel\RequestQuoteTemplate\CollectionFactory;
     */
    protected $collectionFactory;
    protected $readyToPayoutId = 1;
    protected $enableMoveToPayout;

    protected $_shippingCalculationFactory;

    const STATUS_COMPLETE = 'complete';
    const STATUS_SHIPPED = 'Shipped';
    const STATUS_INVOICED = 'Invoiced';

    /**
     * MassMoveToPayout constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param Filter $filter
     * @param VendorOrderCollectionFactory $vendorOrderCollectionFactory
     * @param OrderRepositoryInterface $orderRepositoryInterface
     * @param OrderItemRepositoryInterface $orderItemRepository
     * @param VendorOrder $vendorOrderResource
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        Filter $filter,
        VendorOrderCollectionFactory $vendorOrderCollectionFactory,
        OrderRepositoryInterface $orderRepositoryInterface,
        OrderItemRepositoryInterface $orderItemRepository,
        VendorOrder $vendorOrderResource,
        ShippingCalculationFactory $shippingCalculationFactory
    ) {
        $this->filter = $filter;
        $this->vendorOrderCollectionFactory = $vendorOrderCollectionFactory;
        $this->orderRepository = $orderRepositoryInterface;
        $this->orderItemRepository = $orderItemRepository;
        $this->vendorOrderResource = $vendorOrderResource;
        $this->_shippingCalculationFactory = $shippingCalculationFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    /**
     * Execute controller
     * @return Magento\Framework\Controller\ResultFactor
     */
    public function execute() {
        $collection = $this->filter->getCollection($this->vendorOrderCollectionFactory->create());
        $vendorId = $this->getRequest()->getParam('vendor_id');
        if (empty($vendorId)) {
            $vendorId = $this->_session->getCurrentVendorId();
        }
        if (!empty($vendorId)) {
            $collection->addFieldToFilter('vendor_id', $vendorId);
        }

        $payoutsUpdated = 0;
        foreach ($collection as $payout) {
            //$orderId = $payout->getOrderId();
            //if ($this->enableMoveToPayout($orderId, $vendorId)) { //commented as per OCOM-732
                $payout->setData(
                        'payout_action', $this->readyToPayoutId
                );
                $payout->save();
                $payoutsUpdated++;

                $vendorShipments = $this->_shippingCalculationFactory
                    ->create()
                    ->getCollection()
                    ->addFieldToFilter('order_id', $payout->getOrderId())
                    ->addFieldToFilter('vendor_id', $payout->getVendorId())
                    ->addFieldToFilter('ship_by_type', '2');

            // Update shipping record to ready to payout
            if ($vendorShipments->getSize() > 0) {
                foreach($vendorShipments as $vendorShipment) {
                    $vendorShipment->setType('ready_to_payout');
                    $vendorShipment->save();
                }
            }
            //}
        }

        if ($payoutsUpdated) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were updated.', $payoutsUpdated));
        }

        $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('omnyfy_mcm/pendingpayouts/view', ['vendor_id' => $vendorId]);
        return $resultRedirect;
    }

    public function getOrderStatus($orderId) {
        $order = $this->orderRepository->get($orderId);
        $state = $order->getState();
        return $state;
    }

    public function getOrderItemStatus($itemId) {
        $order = $this->orderItemRepository->get($itemId);
        $state = $order->getStatus();
        return $state;
    }

    public function enableMoveToPayout($orderId, $vendorId) {
        if ($this->getOrderStatus($orderId) == self::STATUS_COMPLETE) {
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

}
