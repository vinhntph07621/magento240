<?php
/**
 * Project: Omnyfy Multi Vendor.
 * User: jing
 * Date: 6/4/17
 * Time: 11:48 AM
 */
namespace Omnyfy\Vendor\Cron;

use Magento\Framework\Event\ManagerInterface;

class CreateShipment
{
    protected $eventManager;

    public function __construct(ManagerInterface $eventManager)
    {
        $this->eventManager = $eventManager;

    }

    public function execute()
    {
        //TODO: Take an order out from a queue, load all order items and group them by locations.
        //TODO: generate shipment for each location
    }
}