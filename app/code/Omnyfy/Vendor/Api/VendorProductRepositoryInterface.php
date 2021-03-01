<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 21/11/19
 * Time: 2:02 pm
 */
namespace Omnyfy\Vendor\Api;

interface VendorProductRepositoryInterface
{
    /**
     * @param int $productId
     * @return \Omnyfy\Core\Api\Json
     */
    public function getByProduct($productId);

    /**
     * @param int $productId
     * @param int $vendorId
     * @return \Omnyfy\Core\Api\Json
     */
    public function assignToVendor($productId, $vendorId);

    /**
     * @param int $productId
     * @param int[] $vendorIds
     * @return \Omnyfy\Core\Api\Json
     */
    public function updateByProduct($productId, $vendorIds);

    /**
     * @param int $productId
     * @param int $vendorId
     * @return \Omnyfy\Core\Api\Json
     */
    public function removeRelation($productId, $vendorId);
}
 