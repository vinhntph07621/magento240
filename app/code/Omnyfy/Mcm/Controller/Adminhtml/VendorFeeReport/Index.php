<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\VendorFeeReport;

class Index extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::marketplace_summaryearning_reports_byvendor';
    protected $adminTitle = 'Marketplace Fees Summary Earnings Report - By Vendor';

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Mcm::marketplace_summaryearning_reports_byvendor');
        $resultPage->getConfig()->getTitle()->prepend(__('Marketplace Fees Summary Earnings Report - By Vendor'));
        $resultPage->addBreadcrumb(__('Omnyfy Earnings Report - By Vendor'), __('Omnyfy Earnings Report - By Vendor'));
        $resultPage->addBreadcrumb(__('Marketplace Fees Summary Earnings Report - By Vendor'), __('Marketplace Fees Summary Earnings Report - By Vendor'));
        return $resultPage;
    }

}
