<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 15/6/17
 * Time: 3:59 PM
 */

namespace Omnyfy\Vendor\Observer;

use Magento\Framework\Event\ObserverInterface;
use Omnyfy\Vendor\Model\Resource\Vendor as VendorResource;

class OrderObserver implements ObserverInterface
{
    protected $vendorResource;

    protected $queueHelper;

    protected $orderRepository;

    protected $vendorConfig;

    protected $logger;

    public function __construct(
        VendorResource $vendorResource,
        \Omnyfy\Core\Helper\Queue $queueHelper,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Omnyfy\Vendor\Model\Config $vendorConfig,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->vendorResource = $vendorResource;

        $this->queueHelper = $queueHelper;

        $this->orderRepository = $orderRepository;

        $this->vendorConfig = $vendorConfig;

        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getData('order');
        $orderId = $order->getId();
        if (empty($orderId)) {
            //TODO: throw exception or log error
            return;
        }

        $items = $order->getItems();
        $vendorIds = [];
        foreach($items as $item) {
            if ($item->hasData('vendor_id')) {
                $vendorIds[] = $item->getData('vendor_id');
            }
        }

        $orderVendorRelation = [];
        $vendorIds = array_unique($vendorIds);

        if (empty($vendorIds)) {
            //TODO: throw exception for no vendor ids
            return;
        }

        //save order vendor relationship
        foreach($vendorIds as $vendorId) {
            $orderVendorRelation[] = ['order_id' => $orderId, 'vendor_id' => $vendorId];
        }
        $this->vendorResource->saveOrderRelation($orderVendorRelation);

        //add order Id to queue for vendor total in order to calculate
        $this->queueHelper->sendMsgToQueue('vendor_order_total', json_encode(['order_id' => $orderId]));
        $this->queueHelper->sendMsgToQueue('vendor_notification_email',
            json_encode(
                [
                    'vendor_ids' => $vendorIds,
                    'order_id' => $orderId,
                    'order_number' => $order->getIncrementId()
                ]
            )
        );

        $customerVendorRelation = [];
        $customerId = $order->getCustomerId();
        if (!empty($customerId)) {
            //save customer vendor relationship
            foreach($vendorIds as $vendorId) {
                $customerVendorRelation[] = ['customer_id' => $customerId, 'vendor_id' => $vendorId];
            }
            $this->vendorResource->saveCustomerRelation($customerVendorRelation);
        }

        // Send webhook order data if there is endpoint specified
        if (!empty($this->vendorConfig->getOrderWebhookEndpoint())) {
            $order = $this->orderRepository->get($orderId);

            $orderData = $order->getData();

            $orderJson = [];

            $orderJson['orderData'] = $orderData;

            foreach ($order->getAllItems() as $item) {
                $orderJson['orderItems']['items'][] = $item->getData();
            }

            $orderJson['orderType'] = 'new';
            $orderJson['billingAddress'] = $order->getBillingAddress()->getData();
            
            if(!is_null($order->getShippingAddress())){
                $orderJson['shipping'] = $order->getShippingAddress()->getData();
            }

            $jsonData = json_encode($orderJson);
            $this->logger->debug('-- START API ORDER DATA NEW ORDER --');
            $this->logger->debug(print_r($jsonData, true));
            $this->logger->debug('-- END API ORDER DATA NEW ORDER --');

            $this->logger->debug('Use pending order webhook URL: ' . $this->vendorConfig->getOrderWebhookEndpoint());
            $url = $this->vendorConfig->getOrderWebhookEndpoint();

            // open connection
            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            // execute post
            // Check if endpoint has been sent
            $result = curl_exec($ch);

            // close connection
            curl_close($ch);
        }

    }
}
