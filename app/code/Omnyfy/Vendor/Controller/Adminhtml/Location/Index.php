<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/8/17
 * Time: 9:09 AM
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Location;

class Index extends \Omnyfy\Vendor\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_Vendor::locations';

    protected $resourceKey = 'Omnyfy_Vendor::locations';

    protected $adminTitle = 'Location';

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Locations'));
        $resultPage->addBreadcrumb(__('Omnyfy'), __('Omnyfy'));
        $resultPage->addBreadcrumb(__('Locations'), __('Locations'));
        return $resultPage;
    }

}