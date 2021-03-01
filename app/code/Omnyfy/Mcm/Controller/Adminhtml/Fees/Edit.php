<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\Fees;

class Edit extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::fees';
    protected $adminTitle = 'Marketplace Fees and Charges Management';
    protected $feesFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Mcm\Model\FeesCharges $feesChargesFactory
    ) {
        $this->feesFactory = $feesChargesFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    /**
     * Fees and Charges Edit.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute() {
        $id = $this->getRequest()->getParam('id');
        $model = $this->feesFactory;

        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This fees and charges no longer exists.'));
                $this->_redirect('*/*');
                return;
            }
        }

        $this->_coreRegistry->register('current_model', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Mcm::fees');
        if ($id) {
            $resultPage->getConfig()->getTitle()->prepend(__("Marketplace Fees and Charges for '%1'", $model->getVendorName()));
        } else {
            $resultPage->getConfig()->getTitle()->prepend(__('Marketplace Fees and Charges'));
        }
        //$resultPage->addBreadcrumb(__('Fees Management'), __('Fees Management'));
        $resultPage->addBreadcrumb(__('Marketplace Fees and Charges Management'), __('Marketplace Fees and Charges Management'));

        $this->_view->renderLayout();
    }

}
