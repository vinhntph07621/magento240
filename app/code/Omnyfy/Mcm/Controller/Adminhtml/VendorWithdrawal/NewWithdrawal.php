<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\VendorWithdrawal;

use Omnyfy\Mcm\Model\VendorWithdrawalHistory;
use Omnyfy\Mcm\Model\VendorPayout;
use Magento\Framework\Exception\LocalizedException;

class NewWithdrawal extends \Omnyfy\Mcm\Controller\Adminhtml\AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::vendor_withdrawal';
    protected $adminTitle = 'New Withdrawal';
    protected $feesFactory;

    protected $vendorPayoutFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Mcm\Model\FeesChargesFactory $feesChargesFactory,
        \Omnyfy\Mcm\Model\VendorWithdrawalHistoryFactory $vendorWithdrawalHistory,
        \Omnyfy\Mcm\Model\VendorPayoutFactory $vendorPayoutFactory
    ) {
        $this->vendorWithdrawalHistory = $vendorWithdrawalHistory;
        $this->vendorPayoutFactory = $vendorPayoutFactory;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    /**
     * Fees and Charges Edit.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute() {
        $vendorInfo = $this->_session->getVendorInfo();
        if (!empty($vendorInfo) && isset($vendorInfo['vendor_id'])) {
            $sessionVendorId = $vendorInfo['vendor_id'];
        }

        $vendorId = $this->getRequest()->getParam('vendor_id');

        if (!empty($sessionVendorId)) {
            $vendorId = $sessionVendorId;
        }

        $model = $this->vendorPayoutFactory->create();

        if ($vendorId) {
            $model = $model->load($vendorId, 'vendor_id');
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(
                        __("This vendor doesn't exist in Marketplace Commercials Management.")
                );
                $this->_redirect('*/*/');
                return;
            } else {
                $vendorInfo = $this->_session->getVendorInfo();
                if (!empty($vendorInfo)) {
                    if ($vendorInfo['vendor_id'] != $vendorId) {
                        $this->messageManager->addErrorMessage(
                                __("You can not withdrawal on behalf of other vendor.")
                        );
                        $this->_redirect('*/*/');
                        return;
                    }
                }

                $eWalletId = $model->getEwalletId();
                $accountRef = $model->getAccountRef();
                $thirdPartyAccountId = $model->getThirdPartyAccountId();
                if (empty($eWalletId) || empty($accountRef) || empty($thirdPartyAccountId)) {
                    $this->messageManager->addErrorMessage(
                        __('Bank account info is missing')
                    );
                    $this->_redirect('*/*/');
                    return;
                }
            }
        } else {
            $this->messageManager->addErrorMessage(
                    __("This vendor doesn't exist in Marketplace Commercials Management.")
            );
            $this->_redirect('*/*/');
            return;
        }

        $this->_coreRegistry->register('current_model', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_Mcm::vendor_withdrawal');

        $resultPage->getConfig()->getTitle()->prepend(__($this->adminTitle));

        $resultPage->addBreadcrumb(__("Vendor's Withdrawals"), __('New Withdrawal'));

        $this->_view->renderLayout();
    }

}
