<?php
namespace Omnyfy\Vendor\Observer;

use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;

class OrderSaveAfter  implements ObserverInterface
{
    protected $logger;
    protected $orderRepository;
    protected $vendorConfig;

    public function __construct(
        LoggerInterface $logger,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Omnyfy\Vendor\Model\Config $vendorConfig
    )
    {
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->vendorConfig = $vendorConfig;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        // Send webhook order data if there is endpoint specified
        if (!empty($this->vendorConfig->getOrderWebhookEndpoint())) {
            $order = $observer->getEvent()->getOrder();

            $orderData = $order->getData();

            $orderJson = [];

            $orderJson['orderData'] = $orderData;

            foreach ($order->getAllItems() as $item) {
                $orderJson['orderItems']['items'][] = $item->getData();
            }

            $orderJson['orderType'] = 'existing';
            $orderJson['billingAddress'] = $order->getBillingAddress()->getData();
            
            if(!is_null($order->getShippingAddress())){
                $orderJson['shipping'] = $order->getShippingAddress()->getData();
            }

            $jsonData = json_encode($orderJson);

            $this->logger->debug('-- START API ORDER DATA --');
            $this->logger->debug(print_r($jsonData, true));
            $this->logger->debug('-- END API ORDER DATA --');

            // If an order is complete, use the complete order webhook URL
            if ($order->getStatus() == 'complete' && $order->getState() == 'complete') {
                $this->logger->debug('Use complete webhook URL: ' . $this->vendorConfig->getCompleteOrderWebhookEndpoint());
                $url = $this->vendorConfig->getCompleteOrderWebhookEndpoint();
            } else {
                $this->logger->debug('Use pending order webhook URL: ' . $this->vendorConfig->getOrderWebhookEndpoint());
                $url = $this->vendorConfig->getOrderWebhookEndpoint();
            }

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
