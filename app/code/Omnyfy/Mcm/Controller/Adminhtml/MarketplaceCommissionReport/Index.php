<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\MarketplaceCommissionReport;

class Index extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::marketplace_summaryearning_reports';
    protected $adminTitle = 'Marketplace Fees Summary Earnings Report - By Order';

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Mcm::marketplace_summaryearning_reports');
        $resultPage->getConfig()->getTitle()->prepend(__('Marketplace Fees Summary Earnings Report - By Order'));
        $resultPage->addBreadcrumb(__('Omnyfy Commission'), __('Omnyfy Commission'));
        $resultPage->addBreadcrumb(__('Marketplace Fees Summary Earnings Report - By Order'), __('Marketplace Fees Summary Earnings Report - By Order'));
        return $resultPage;
    }

}
