<?php
/**
 * Project: MCM.
 * User: jing
 * Date: 2018-12-20
 * Time: 14:43
 */
namespace Omnyfy\Mcm\Observer;

use \Omnyfy\Core\Helper\Queue;

class AddOrderToQueue implements \Magento\Framework\Event\ObserverInterface
{
    protected $_queueHelper;

    public function __construct(Queue $queueHelper)
    {
        $this->_queueHelper = $queueHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getData('order');
        $shipment = $observer->getData('shipment');
        $eventName = $observer->getEvent()->getName();

        $topic = 'mcm_order';
        $orderId = 0;
        switch($eventName) {
            case 'checkout_submit_all_after':
                $topic = 'mcm_after_place_order';
                if (empty($order)) {
                    //LOG error
                    return;
                }
                $orderId = $order->getId();
                break;
            case 'sales_order_shipment_save_after':
                $topic = 'mcm_payout_order';
                if (empty($shipment)) {
                    //LOG error
                    return;
                }
                $orderId = $shipment->getOrderId();
                break;
        }

        if (0==$orderId || 'mcm_order' == $topic) {
            return;
        }

        $this->_queueHelper->sendMsgToQueue($topic, json_encode(['order_id' => $orderId]));
    }
}