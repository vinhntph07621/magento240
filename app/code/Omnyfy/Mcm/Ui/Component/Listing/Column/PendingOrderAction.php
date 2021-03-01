<?php

namespace Omnyfy\Mcm\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Omnyfy\Mcm\Model\ResourceModel\VendorOrder;

class PendingOrderAction extends Column {

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    protected $orderRepository;

    const STATUS_COMPLETE = 'complete';
    const STATUS_SHIPPED = 'Shipped';
    const STATUS_INVOICED = 'Invoiced';

    protected $enableMoveToPayout = 0;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
    ContextInterface $context, UiComponentFactory $uiComponentFactory, UrlInterface $urlBuilder, OrderRepositoryInterface $orderRepositoryInterface, OrderItemRepositoryInterface $orderItemRepositoryInterface, VendorOrder $vendorOrderResource, array $components = [], array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->orderRepository = $orderRepositoryInterface;
        $this->orderItemRepositoryInterface = $orderItemRepositoryInterface;
        $this->vendorOrderResource = $vendorOrderResource;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource) {
        if (isset($dataSource['data']['items'])) {

            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')]['view'] = [
                    'href' => $this->urlBuilder->getUrl(
                            'sales/order/view', ['order_id' => $item['order_id']]
                    ),
                    'label' => __('View'),
                    'hidden' => false,
                ];

                //if ($this->enableMoveToPayout($item['order_id'], $item['vendor_id'])) { //commented as per OCOM-732
                    $item[$this->getData('name')]['move_to_payout'] = [
                        'href' => $this->urlBuilder->getUrl(
                                'omnyfy_mcm/pendingpayouts/movetopayout', ['id' => $item['id'], 'vendor_id' => $item['vendor_id']]
                        ),
                        'label' => __('Move to Payout'),
                        'hidden' => false,
                    ];
                //}
            }
        }

        return $dataSource;
    }

    public function getOrderStatus($orderId) {
        $order = $this->orderRepository->get($orderId);
        $state = $order->getState();
        return $state;
    }

    public function getOrderItemStatus($itemId) {
        $order = $this->orderItemRepositoryInterface->get($itemId);
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
