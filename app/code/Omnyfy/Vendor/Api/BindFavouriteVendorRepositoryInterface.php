<?php
/**
 * Project: apcd.
 * User: seth
 * Date: 6/9/19
 * Time: 2:33 PM
 */
namespace Omnyfy\Vendor\Api;

interface BindFavouriteVendorRepositoryInterface
{
    /**
     * @param int $customerId
     * @param int $vendorId
     * @return \Omnyfy\Core\Api\Json
     */
    public function save($customerId, $vendorId);

    /**
     * @param int $customerId
     * @param int $vendorId
     * @return \Omnyfy\Core\Api\Json
     */
    public function delete($customerId, $vendorId);
}