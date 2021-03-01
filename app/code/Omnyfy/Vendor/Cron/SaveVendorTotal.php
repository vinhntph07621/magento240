<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 1/8/17
 * Time: 3:23 PM
 */
namespace Omnyfy\Vendor\Cron;

use Magento\Framework\Event\ManagerInterface;

class SaveVendorTotal
{
    protected $eventManager;

    protected $vendorHelper;

    public function __construct(
        ManagerInterface $eventManager,
        \Omnyfy\Vendor\Helper\Data $vendorHelper
    )
    {
        $this->eventManager = $eventManager;

        $this->vendorHelper = $vendorHelper;
    }

    public function execute()
    {
        while($qItem = $this->vendorHelper->takeMsgFromQueue('vendor_order_total')) {
            if (!isset($qItem['id']) || empty($qItem['id'])) {
                continue;
            }
            if (!isset($qItem['message']) || empty($qItem['message'])) {
                $this->vendorHelper->updateQueueMsgStatus($qItem['id'], 'blocking');
                continue;
            }
            $itemData = json_decode($qItem['message'], true);
            if (empty($itemData) || !isset($itemData['order_id'])) {
                $this->vendorHelper->updateQueueMsgStatus($qItem['id'], 'blocking');
                continue;
            }
            $orderId = $itemData['order_id'];

            $result = $this->vendorHelper->calculateVendorOrderTotal($orderId);

            if ($result) {
                $this->vendorHelper->updateQueueMsgStatus($qItem->getId(), 'done');
            }
            else{
                $this->vendorHelper->updateQueueMsgStatus($qItem->getId(), 'failed');
            }
        }

    }
}