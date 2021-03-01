<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 21/11/19
 * Time: 4:54 pm
 */
namespace Omnyfy\Vendor\Model\Location;

class Inventory extends \Magento\Framework\DataObject implements \Omnyfy\Vendor\Api\Data\InventoryInterface
{
    public function getLocationId()
    {
        return $this->getData(self::LOCATION_ID);
    }

    public function setLocationId($locationId)
    {
        return $this->setData(self::LOCATION_ID, $locationId);
    }

    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }
}
 