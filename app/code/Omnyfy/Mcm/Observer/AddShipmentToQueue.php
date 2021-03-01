<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-26
 * Time: 11:46
 */
namespace Omnyfy\Mcm\Observer;

use \Omnyfy\Core\Helper\Queue;

class AddShipmentToQueue implements \Magento\Framework\Event\ObserverInterface
{
    protected $_queueHelper;

    public function __construct(Queue $queueHelper)
    {
        $this->_queueHelper = $queueHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $shipment = $observer->getData('shipment');
        $eventName = $observer->getEvent()->getName();

        if (empty($shipment) || 'sales_order_shipment_save_after' != $eventName) {
            //LOG error
            return;
        }

        $topic = 'mcm_payout_order';

        $this->_queueHelper->sendMsgToQueue($topic, json_encode(['shipment_id' => $shipment->getId()]));
    }
}
 