<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 12/9/19
 * Time: 3:18 pm
 */
namespace Omnyfy\VendorSubscription\Api;

use Omnyfy\VendorSubscription\Api\Data\UpdateInterface;

interface UpdateRepositoryInterface
{
    /**
     * @param int $updateId
     * @param bool $forceReload
     * @return \Omnyfy\VendorSubscription\Api\Data\UpdateInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($updateId, $forceReload=false);
}
 