<?php
/**
 * Project: apcd.
 * User: jing
 * Date: 19/11/18
 * Time: 2:04 AM
 */
namespace Omnyfy\Vendor\Api;

interface BindRepositoryInterface
{
    /**
     * @param int $customerId
     * @param int $vendorId
     * @return \Omnyfy\Core\Api\Json
     */
    public function save($customerId, $vendorId);
}