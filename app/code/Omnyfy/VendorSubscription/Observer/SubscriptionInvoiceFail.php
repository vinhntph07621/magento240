<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-09
 * Time: 15:12
 */
namespace Omnyfy\VendorSubscription\Observer;

class SubscriptionInvoiceFail implements \Magento\Framework\Event\ObserverInterface
{
    protected $historyResource;

    protected $helper;

    protected $_logger;

    public function __construct(
        \Omnyfy\VendorSubscription\Model\Resource\History $historyResource,
        \Omnyfy\VendorSubscription\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->historyResource = $historyResource;
        $this->helper = $helper;
        $this->_logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $observer->getData('data');

        //load subscription by sub_gateway_id
        $subscription = $this->helper->loadSubscriptionByGatewayId($data['sub_gateway_id']);
        if (empty($subscription)) {
            $this->_logger->error('Missing subscription', $data);
            return;
        }

        //save success history
        $data['plan_id'] = $subscription->getPlanId();
        $data['vendor_id'] = $subscription->getVendorId();
        $data['vendor_name'] = $subscription->getVendorName();
        $data['subscription_id'] = $subscription->getId();
        $data['plan_price'] = $subscription->getPlanPrice();
        $this->historyResource->bulkSave([$data]);

        //Leave disable vendor logic somewhere else
    }
}
 