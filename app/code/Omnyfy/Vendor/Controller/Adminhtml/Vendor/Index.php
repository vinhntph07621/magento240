<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/6/17
 * Time: 4:24 PM
 */

namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor;

use Omnyfy\Vendor\Controller\Adminhtml\AbstractAction;

class Index extends AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::vendors';
    protected $resourceKey = 'Omnyfy_Vendor::vendors';

    protected $adminTitle = 'Vendors';

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Vendors'));
        $resultPage->addBreadcrumb(__('Omnyfy'), __('Omnyfy'));
        $resultPage->addBreadcrumb(__('Vendors'), __('Vendors'));
        return $resultPage;
    }
}