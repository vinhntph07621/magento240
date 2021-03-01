<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\VendorReport;

class Index extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::vendor_fee_reports';
    protected $adminTitle = 'Vendor Fee Report';

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Mcm::vendor_fee_reports');
        $resultPage->getConfig()->getTitle()->prepend(__('Vendor Fee Report'));
        $resultPage->addBreadcrumb(__('Omnyfy Vendor Report'), __('Omnyfy Vendor Report'));
        $resultPage->addBreadcrumb(__('Vendor Fee Report'), __('Vendor Fee Report'));
        return $resultPage;
    }

}
