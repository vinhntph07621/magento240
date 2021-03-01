<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\PendingPayouts;

class View extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::pending_payouts';
    protected $adminTitle = 'Payouts Detail View';
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
        $vendorId = $this->getRequest()->getParam('vendor_id');
        $this->_session->setCurrentVendorId($vendorId);
        $model = $this->vendorPayoutFactory->create();
        $model = $model->load($vendorId, 'vendor_id');
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Mcm::pending_payouts');
        $resultPage->getConfig()->getTitle()->prepend(__('Payouts Detail View for '. $model->getVendorName()));
        $resultPage->addBreadcrumb(__('Pending Payouts'), __('Payouts Detail'));
        return $resultPage;
    }

}
