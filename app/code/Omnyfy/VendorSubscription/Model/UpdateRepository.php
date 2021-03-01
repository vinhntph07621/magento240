<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 12/9/19
 * Time: 3:21 pm
 */
namespace Omnyfy\VendorSubscription\Model;

use Magento\Framework\Exception\NoSuchEntityException;

class UpdateRepository implements \Omnyfy\VendorSubscription\Api\UpdateRepositoryInterface
{
    protected $updateFactory;

    protected $instances = [];

    public function __construct(
        \Omnyfy\VendorSubscription\Model\UpdateFactory $updateFactory
    ) {
        $this->updateFactory = $updateFactory;
    }

    public function getById($updateId, $forceReload = false)
    {
        if (!isset($this->instances[$updateId]) || $forceReload) {
            $update = $this->updateFactory->create();
            $update->load($updateId);
            if (!$update->getId()) {
                throw new NoSuchEntityException(__('Request Subscription Update does not exist'));
            }
            $this->instances[$updateId] = $update;
        }
        return $this->instances[$updateId];
    }
}
 