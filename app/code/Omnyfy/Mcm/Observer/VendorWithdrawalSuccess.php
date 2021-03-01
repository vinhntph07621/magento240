<?php

namespace Omnyfy\Mcm\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Omnyfy\Mcm\Model\VendorWithdrawalHistory;
use Omnyfy\Mcm\Model\VendorPayout;

class VendorWithdrawalSuccess implements ObserverInterface {

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(VendorWithdrawalHistory $vendorWithdrawalHistoryFactory, VendorPayout $vendorPayoutFactory, \Magento\Backend\Model\UrlInterface $backendUrl, \Omnyfy\Mcm\Helper\Data $helper, \Omnyfy\Vendor\Model\Vendor $vendorModel, \Omnyfy\Mcm\Helper\Email $emailHelper) {

        $this->vendorWithdrawalHistoryFactory = $vendorWithdrawalHistoryFactory;
        $this->vendorPayoutFactory = $vendorPayoutFactory;
		$this->_backendUrl = $backendUrl;
		$this->_helper = $helper;
		$this->_vendorModel = $vendorModel;
		$this->_emailHelper = $emailHelper;
    }

    public function execute(EventObserver $observer) {

        $withdrawalSuccessData = $observer->getEvent()->getData();
		$withdrawalSuccessData = $withdrawalSuccessData['data'];
		$withdrawalSuccessData['ext_info'] = json_decode($withdrawalSuccessData['ext_info'],true);
		\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug('== Withdrawal callback == ');
        if (!empty($withdrawalSuccessData)) {
            $vendorWithdrawalHistoryModel = $this->vendorWithdrawalHistoryFactory->load($withdrawalSuccessData['ext_info']['withdrawal_history_id']);
            if (!empty($vendorWithdrawalHistoryModel->getData())) {
                $vendorWithdrawalHistoryModel->setStatus(1); //Vendor withdrawal history: 0 = Fail, 1 = Success, 2 = In progress
                $vendorWithdrawalHistoryModel->save();
                $this->updateVendorWallet($withdrawalSuccessData);
				/* start send Withdrawal notification mail to Vendor*/
				$vendorModel = $this->_vendorModel->load($withdrawalSuccessData['ext_info']['vendor_id']);
				/* Receiver Detail  */
				$vendorReceiverInfo = [
					'name' => $vendorModel->getName(),
					'email' => $vendorModel->getEmail()
				];
				/* Sender Detail  */
				$senderInfo =array();
				/* Assign values for your template variables  */
				$vendorEmailTempVariables = array();
				$vendorEmailTempVariables['amount'] = $this->_helper->formatToBaseCurrency($withdrawalSuccessData['amount']);
				$vendorEmailTempVariables['vendor_name'] = $vendorModel->getName();
				$vendorEmailTempVariables['withdrawal_history_link'] = $this->_backendUrl->getUrl('omnyfy_mcm/vendorWithdrawal/index/id/'.$withdrawalSuccessData['ext_info']['withdrawal_history_id']);
						
				/* call send mail method from helper or where it define */ 
				$this->_emailHelper->mcmMailSend(
					'withdrawal_success_notification_to_vendor_email_template',
					$vendorEmailTempVariables,
					$senderInfo,
					$vendorReceiverInfo
				);
				\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug('== Withdrawal callback mail send == ');
				/* end send Withdrawal notification mail to Vendor*/
            }
        }
        
    }
    
    /**
     * Deduct the e-wallet withdrawal amount to vendor wallet balance 
     * 
     * @param type $withdrawalSuccessData
     */
    protected function updateVendorWallet($withdrawalSuccessData) {
        $payoutModel = $this->vendorPayoutFactory->load($withdrawalSuccessData['ext_info']['vendor_id'], 'vendor_id');
        $payoutModel->setEwalletBalance($payoutModel->getEwalletBalance() - $withdrawalSuccessData['amount']);
        $payoutModel->save();
    }
}
