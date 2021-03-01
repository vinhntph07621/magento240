<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\VendorEarning;

class Index extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::vendor_earning';
    protected $adminTitle = 'Vendor Earnings';

    protected $vendorPayoutFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Mcm\Model\VendorPayoutFactory $vendorPayoutFactory
    ) {
        $this->vendorPayoutFactory = $vendorPayoutFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    public function execute() {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Mcm::vendor_earning');
        $vendorId = $this->getRequest()->getParam('vendor_id');
        if (!$vendorId) {
            $vendorInfo = $this->_session->getVendorInfo();
            if (!empty($vendorInfo)) {
                $vendorId = $vendorInfo['vendor_id'];
                //$this->_redirect('*/*/*', ['vendor_id' => $vendorId]);
            }
        }
        if ($vendorId) {
            $vendorName = '';
            $model = $this->vendorPayoutFactory->create();
            $collection = $model->getCollection()->addFieldToFilter('vendor_id', $vendorId);
            if (!empty($collection->getItems())) {
                $model = $model->load($vendorId, 'vendor_id');
                $vendorName = $model->getVendorName();
                //$resultPage->getConfig()->getTitle()->set('Earnings for ' . $vendorName);
                $this->_view->getPage()->getConfig()->getTitle()->prepend('Earnings for ' . $vendorName);
            } else {
                if ($vendorId) {
                    $this->messageManager->addErrorMessage(
                            __("This vendor doesn't exist in Marketplace Commercials Management.")
                    );
                }
            }
        } else {
            $this->_view->getPage()->getConfig()->getTitle()->prepend($this->adminTitle);
        }


        $resultPage->addBreadcrumb(__('Vendor Earnings'), __('Vendor Earnings'));
        return $resultPage;
    }

}
