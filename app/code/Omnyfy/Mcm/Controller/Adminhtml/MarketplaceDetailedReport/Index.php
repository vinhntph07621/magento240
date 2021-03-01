<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\MarketplaceDetailedReport;

class Index extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::marketplace_detailedearning_reports';
    protected $adminTitle = 'Marketplace Fees Detailed Earnings Report';

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Mcm::marketplace_detailedearning_reports');
        $resultPage->getConfig()->getTitle()->prepend(__('Marketplace Fees Detailed Earnings Report'));
        $resultPage->addBreadcrumb(__('Omnyfy'), __('Omnyfy'));
        $resultPage->addBreadcrumb(__('Marketplace Fees Detailed Earnings Report'), __('Marketplace Fees Detailed Earnings Report'));
        return $resultPage;
    }

}
