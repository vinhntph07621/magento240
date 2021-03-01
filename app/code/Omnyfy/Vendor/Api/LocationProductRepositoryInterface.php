<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 21/11/19
 * Time: 2:07 pm
 */
namespace Omnyfy\Vendor\Api;

interface LocationProductRepositoryInterface
{
    /**
     * @param int $productId
     * @return \Omnyfy\Core\Api\Json
     */
    public function getByProduct($productId);

    /**
     * @param int $productId
     * @param \Omnyfy\Vendor\Api\Data\InventoryInterface[] $inventories
     * @return \Omnyfy\Core\Api\Json
     */
    public function createInventory($productId, $inventories);

    /**
     * @param int $productId
     * @param \Omnyfy\Vendor\Api\Data\InventoryInterface[] $inventories
     * @return \Omnyfy\Core\Api\Json
     */
    public function updateInventory($productId, $inventories);

    /**
     * @param int $productId
     * @param int $locationId
     * @return \Omnyfy\Core\Api\Json
     */
    public function removeRelation($productId, $locationId);
}
 