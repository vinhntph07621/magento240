<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-08
 * Time: 17:32
 */
namespace Omnyfy\VendorSubscription\Controller\Adminhtml\Subscription;

class Index extends \Omnyfy\VendorSubscription\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_VendorSubscription::subscriptions';

    protected $adminTitle = 'Subscriptions';

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        return $resultPage;
    }
}