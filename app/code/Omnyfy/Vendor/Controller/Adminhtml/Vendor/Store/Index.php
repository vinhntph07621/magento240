<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-10
 * Time: 15:06
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor\Store;

use Omnyfy\Vendor\Controller\Adminhtml\AbstractAction;

class Index extends AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::vendor_stores';
    protected $resourceKey = 'Omnyfy_Vendor::vendor_stores';

    protected $adminTitle = 'Vendor Store View';

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Vendor Store'));
        $resultPage->addBreadcrumb(__('Omnyfy'), __('Omnyfy'));
        $resultPage->addBreadcrumb(__('Vendor Store'), __('Vendor Store'));
        return $resultPage;
    }
}
 