<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 21/11/19
 * Time: 3:01 pm
 */
namespace Omnyfy\Vendor\Api\Data;

interface InventoryInterface
{
    const LOCATION_ID = 'location_id';

    const QTY = 'qty';

    /**
     * @return int
     */
    public function getLocationId();

    /**
     * @param int $locationId
     * @return $this
     */
    public function setLocationId($locationId);

    /**
     * @return string
     */
    public function getQty();

    /**
     * @param string $qty
     * @return $this
     */
    public function setQty($qty);
}
 