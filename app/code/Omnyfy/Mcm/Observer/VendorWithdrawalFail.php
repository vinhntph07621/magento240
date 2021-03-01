<?php

namespace Omnyfy\Mcm\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Omnyfy\Mcm\Model\VendorWithdrawalHistory;
use Omnyfy\Mcm\Model\VendorPayout;

class VendorWithdrawalFail implements ObserverInterface {

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(VendorWithdrawalHistory $vendorWithdrawalHistoryFactory, VendorPayout $vendorPayoutFactory) {

        $this->vendorWithdrawalHistoryFactory = $vendorWithdrawalHistoryFactory;
        $this->vendorPayoutFactory = $vendorPayoutFactory;
    }

    public function execute(EventObserver $observer) {

        $withdrawalFailData = $observer->getEvent()->getData();
        $withdrawalFailData = $withdrawalFailData['data'];
		$withdrawalFailData['ext_info'] = json_decode($withdrawalFailData['ext_info'],true);

        if (!empty($withdrawalFailData)) {
            $vendorWithdrawalHistoryModel = $this->vendorWithdrawalHistoryFactory->load($withdrawalFailData['ext_info']['withdrawal_history_id']);
            if (!empty($vendorWithdrawalHistoryModel)) {
                $vendorWithdrawalHistoryModel->setStatus(0); //Vendor withdrawal history: 0 = Fail, 1 = Success, 2 = In progress
                $vendorWithdrawalHistoryModel->save();
            }
        }
        //$this->updateVendorWallet($withdrawalFailData);
    }

    /*
    protected function updateVendorWallet($withdrawalFailData) {
        //add the failed amount to vendor wallet balance 
        $payoutModel = $this->vendorPayoutFactory->load($withdrawalFailData['ext_info']['vendor_id'], 'vendor_id');
        $payoutModel->setEwalletBalance($payoutModel->getEwalletBalance() + $withdrawalFailData['amount']);
        $payoutModel->save();
    }*/

}
