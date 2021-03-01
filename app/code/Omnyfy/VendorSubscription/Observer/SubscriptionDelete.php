<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-12
 * Time: 14:27
 */
namespace Omnyfy\VendorSubscription\Observer;

class SubscriptionDelete implements \Magento\Framework\Event\ObserverInterface
{
    protected $helper;

    protected $_logger;

    public function __construct(
        \Omnyfy\VendorSubscription\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->_logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $observer->getData('data');
        $subscription = $this->helper->loadSubscriptionByGatewayId($data['gateway_id']);
        if (empty($subscription)) {
            $this->_logger->error('Missing subscription', $data);
            return;
        }

        $subscription->addData($data);
        $subscription->save();

        $this->helper->disableVendor($subscription->getVendorId());
    }
}
 