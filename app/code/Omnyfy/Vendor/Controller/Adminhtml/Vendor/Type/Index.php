<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-08
 * Time: 15:55
 */

namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor\Type;

use Omnyfy\Vendor\Controller\Adminhtml\AbstractAction;

class Index extends AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::vendor_type';
    protected $resourceKey = 'Omnyfy_Vendor::vendor_type';

    protected $adminTitle = 'Vendor Types';

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Vendor Types'));
        $resultPage->addBreadcrumb(__('Omnyfy'), __('Omnyfy'));
        $resultPage->addBreadcrumb(__('Vendor Types'), __('Vendor Types'));
        return $resultPage;
    }
}