<?php

namespace Omnyfy\Mcm\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Omnyfy\Mcm\Model\ResourceModel\VendorOrder\Collection as VendorOrderCollectionFactory;
use Omnyfy\Mcm\Model\ResourceModel\VendorPayoutHistory\Collection as VendorPayoutHistoryCollectionFactory;
use Omnyfy\Mcm\Model\VendorPayout;

class VendorPayoutFail implements ObserverInterface {

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(VendorOrderCollectionFactory $vendorOrderCollectionFactory, VendorPayoutHistoryCollectionFactory $vendorPayoutHistoryCollectionFactory, VendorPayout $vendorPayoutFactory) {

        $this->vendorOrderCollectionFactory = $vendorOrderCollectionFactory;
        $this->vendorPayoutHistoryCollectionFactory = $vendorPayoutHistoryCollectionFactory;
        $this->vendorPayoutFactory = $vendorPayoutFactory;
    }

    public function execute(EventObserver $observer) {

        $payoutFailData = $observer->getEvent()->getData();
        $payoutFailData = $payoutFailData['data'];
		$payoutFailData['ext_info'] = json_decode($payoutFailData['ext_info'],true);

        if (!empty($payoutFailData)) {
            $vendorOrderCollection = $this->vendorOrderCollectionFactory
                    ->addFieldToFilter('id', ['in' => $payoutFailData['ext_info']['vendor_order_ids']])
                    ->addFieldToFilter('vendor_id', $payoutFailData['ext_info']['vendor_id']);
            if (!empty($vendorOrderCollection)) {
                /**
                 * payout_status = 2 and payout_action = 2 means Refunded
                 */
                foreach ($vendorOrderCollection as $vendorOrder) {
                    $vendorOrder->setPayoutStatus(0); //Vendor Payout Status: 0 = Unpaid, 1 = Paid, 2 = Refund, 3 = In progress
                    $vendorOrder->setPayoutAction(1); //Order Payout Action: 0 = Pending, 1 = Added to payout, 2 = Refunded
                    $vendorOrder->save();
                }
            }

            $this->updateVendorPayoutHistory($payoutFailData);
            //$this->updateVendorWallet($payoutFailData);
        }
    }

    protected function updateVendorPayoutHistory($payoutFailData) {
        $vendorPayoutHistoryCollection = $this->vendorPayoutHistoryCollectionFactory
                ->addFieldToFilter('payout_ref', $payoutFailData['payout_ref'])
                ->addFieldToFilter('vendor_id', $payoutFailData['ext_info']['vendor_id'])
                ->addFieldToFilter('payout_id', $payoutFailData['ext_info']['payout_id'])
                ->addFieldToFilter('vendor_order_id', ['in' => $payoutFailData['ext_info']['vendor_order_ids']]);

        if (!empty($vendorPayoutHistoryCollection)) {
            foreach ($vendorPayoutHistoryCollection as $vendorPayoutHistory) {
                $vendorPayoutHistory->setStatus(0); //Payout Status: 0 = Failed, 1 = Suceess, 2 = In progress
                $vendorPayoutHistory->save();
            }
        }
    }

    protected function updateVendorWallet($payoutFailData) {
        //add the failed amount to vendor wallet balance 
        $payoutModel = $this->vendorPayoutFactory->load($payoutFailData['ext_info']['payout_id']);
        $payoutModel->setEwalletBalance($payoutModel->getEwalletBalance() + $payoutFailData['amount']);
        $payoutModel->save();
    }

}
