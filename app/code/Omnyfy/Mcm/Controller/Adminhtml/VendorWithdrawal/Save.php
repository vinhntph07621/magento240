<?php

namespace Omnyfy\Mcm\Controller\Adminhtml\VendorWithdrawal;

use Omnyfy\Mcm\Controller\Adminhtml\AbstractAction;
use Omnyfy\Mcm\Helper\Data as HelperData;

/**
 * Cms template controller
 */
class Save extends AbstractAction {

    protected $resourceKey = 'Omnyfy_Mcm::vendor_withdrawal';

    protected $withdrawalHistoryFactory;

    protected $payoutFactory;

    protected $pricing;

    /**
     * Save constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Omnyfy\Mcm\Model\VendorWithdrawalHistoryFactory $withdrawalHistoryFactory
     * @param \Omnyfy\Mcm\Model\VendorPayoutFactory $payoutFactory
     * @param \Magento\Framework\Pricing\Helper\Data $pricing
     * @param HelperData $helper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Mcm\Model\VendorWithdrawalHistoryFactory $withdrawalHistoryFactory,
        \Omnyfy\Mcm\Model\VendorPayoutFactory $payoutFactory,
        \Magento\Framework\Pricing\Helper\Data $pricing,
        HelperData $helper
    ) {
        $this->withdrawalHistoryFactory = $withdrawalHistoryFactory;
        $this->payoutFactory = $payoutFactory;
        $this->pricing = $pricing;
        $this->_helper = $helper;
        parent::__construct($context, $coreRegistry, $resultForwardFactory, $resultPageFactory, $authSession, $logger);
    }

    /**
     * Before model save
     * @param  \Omnyfy\Cms\Model\FeesCharges $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _beforeSave($model, $request) {
        
    }

    public function execute() {
        $request = $this->getRequest();

        $id = '';
        if (!$request->isPost()) {
            $this->getResponse()->setRedirect($this->getUrl('*/*'));
        }
        $session = $this->_session;
        $data = $this->getRequest()->getPostValue();

        $model = $this->withdrawalHistoryFactory->create();
        try {
            $inputFilter = new \Zend_Filter_Input(
                    [], [], $data
            );
            $data = $inputFilter->getUnescaped();
            $payoutModel = $this->getPayoutModelData($data['vendor_id']);
            $eWalletId = $payoutModel->getEwalletId();
            $accountRef = $payoutModel->getAccountRef();
            $thirdPartyAccountId = $payoutModel->getThirdPartyAccountId();
            if (empty($eWalletId) || empty($accountRef) || empty($thirdPartyAccountId)) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('One of eWallet ID, Account Ref or Account ID is missing')
                );
            }

            $eWalletBalance = (double) $payoutModel->getEwalletBalance();

            if ((double) $data['withdrawal_amount'] <= $eWalletBalance) {
                $data['available_balance'] = $this->getBalanceAmountAfterWithdrawal($data);
                $data['status'] = 2; //In progress
                $model->setData($data);
                $session->setPageData($data);
                $this->_beforeSave($model, $request);
                if ($model->save()) {
                    //$this->savePayoutInfo($data); //Save Payout info ewallet balance
                    $this->withdrawalSend($payoutModel, $model); //Send to assembly pay for withdrawal to vendor bank account
                    $this->_afterSave($model, $request);

                    $this->messageManager->addSuccessMessage(__('Your withdrawal request has been received.'));
                    $session->setPageData(false);

                    $this->_redirect('omnyfy_mcm/vendorWithdrawal/index/vendor_id/' . $model->getVendorId()); //, ['vendor_id', $model->getVendorId()]
                    return;
                }
            } else {
                $withdrawalLimit = $this->currency($payoutModel->getEwalletBalance());
                throw new \Magento\Framework\Exception\LocalizedException(__('You can withdraw up to ' . $withdrawalLimit));
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $vendorId = (int) $this->getRequest()->getParam('vendor_id');
            if (!empty($vendorId)) {
                $this->_redirect('*/*/newWithdrawal', ['vendor_id' => $data['vendor_id']]);
            } else {
                $this->_redirect('*/*/index');
            }
            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the new withdrawal data. Please review the error log.')
            );
            $this->_logger->critical($e->getMessage());
            $this->_session->setPageData($data);
            if (isset($data['vednor_id'])) {
                $this->_redirect('*/*/newWithdrawal', ['vendor_id' => $data['vednor_id']]);
            } else {
                $this->_redirect('*/*/index');
            }

            return;
        }
        $this->_redirect('*/*/newWithdrawal', ['vendor_id' => $data['vednor_id']]);
    }

    public function withdrawalSend($payoutData, $withdrawalData) {
        /*
         * Integrate with assembly pay for withdrawal to vendor bank account
         */
        $eventData = [
            'wallet_id' => $payoutData->getEwalletId(),
            'account_id' => $payoutData->getThirdPartyAccountId(),
            'amount' => $withdrawalData->getWithdrawalAmount(),
            'ext_info' => [
                'vendor_id' => $payoutData->getVendorId(),
                'payout_id' => $payoutData->getpayoutId(),
                'withdrawal_history_id' => $withdrawalData->getId(),
            ],
        ];
        $this->_eventManager->dispatch('omnyfy_mcm_payout_withdraw', ['data' => $eventData]);
    }

    public function savePayoutInfo($data) {
        if (isset($data['vendor_id'])) {
            $payoutModel = $this->getPayoutModelData($data['vendor_id']);
            $payoutModel->setEwalletBalance($this->getBalanceAmountAfterWithdrawal($data));
            $payoutModel->save();
        }
    }

    public function getBalanceAmountAfterWithdrawal($data) {
        $ewalletBalance = '';
        $payoutModel = $this->getPayoutModelData($data['vendor_id']);
        if (!empty($payoutModel)) {
            $ewalletBalance = $payoutModel->getEwalletBalance() - $data['withdrawal_amount'];
        }
        return $ewalletBalance;
    }

    public function getPayoutModelData($vendorId) {
        $payoutModel = $this->payoutFactory->create()->load($vendorId, 'vendor_id');
        return $payoutModel;
    }

    /**
     * After model save
     * @param  \Omnyfy\Cms\Model\FeesCharges $model
     * @param  \Magento\Framework\App\Request\Http $request
     * @return void
     */
    protected function _afterSave($model, $request) {
        
    }

    public function currency($value) {
        return $this->_helper->convertBasePrice($value, $this->getStoreId());
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId() {
        return $this->_helper->getStoreId();
    }

}
