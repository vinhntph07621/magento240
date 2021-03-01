<?php

namespace Omnyfy\Mcm\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;
use Omnyfy\Mcm\Model\ResourceModel\VendorOrder\Collection as VendorOrderCollectionFactory;
use Omnyfy\Mcm\Model\ResourceModel\VendorPayoutHistory\Collection as VendorPayoutHistoryCollectionFactory;
use Omnyfy\Mcm\Model\VendorPayout;

class VendorPayoutChange implements ObserverInterface {

    /**
     * @var \Omnyfy\Mcm\Helper\Data
     */
    protected $_helper;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(VendorOrderCollectionFactory $vendorOrderCollectionFactory, VendorPayoutHistoryCollectionFactory $vendorPayoutHistoryCollectionFactory, VendorPayout $vendorPayoutFactory, \Magento\Backend\Model\UrlInterface $backendUrl, \Omnyfy\Mcm\Helper\Data $helper, \Omnyfy\Vendor\Model\Vendor $vendorModel, \Omnyfy\Mcm\Helper\Email $emailHelper) {

        $this->vendorOrderCollectionFactory = $vendorOrderCollectionFactory;
        $this->vendorPayoutHistoryCollectionFactory = $vendorPayoutHistoryCollectionFactory;
        $this->vendorPayoutFactory = $vendorPayoutFactory;
        $this->_backendUrl = $backendUrl;
        $this->_helper = $helper;
        $this->_vendorModel = $vendorModel;
        $this->_emailHelper = $emailHelper;
    }

    public function execute(EventObserver $observer) {

        $payoutCallbackData = $observer->getEvent()->getData();
        $payoutCallbackData = $payoutCallbackData['data'];
        $payoutCallbackData['ext_info'] = json_decode($payoutCallbackData['ext_info'], true);
        //\Magento\Framework\App\ObjectManager::getInstance()->get('Psr\Log\LoggerInterface')->debug('== payout callback == ' . print_r($payoutCallbackData, true));
        if (!empty($payoutCallbackData)) {
            if (isset($payoutCallbackData['state'])) {
                if ($payoutCallbackData['state'] == 'payment_pending' || $payoutCallbackData['state'] == 'pending') {
                    $this->vendorOrderCollectionFactory->clear()->getSelect()->reset(\Zend_Db_Select::WHERE);
                    $vendorOrderCollection = $this->vendorOrderCollectionFactory
                            ->addFieldToFilter('id', ['in' => $payoutCallbackData['ext_info']['vendor_order_ids']])
                            ->addFieldToFilter('vendor_id', $payoutCallbackData['ext_info']['vendor_id'])->load();

                    if (!empty($vendorOrderCollection)) {
                        /**
                         * payout_status = 2 and payout_action = 2 means Refunded
                         */
                        foreach ($vendorOrderCollection as $vendorOrder) {
                            $vendorOrder->setPayoutStatus(4); //Vendor Payout Status: 0 = Unpaid, 1 = Paid, 2 = Refund, 3 = In progress, 4 = Processed - awaiting settlement
                            $vendorOrder->setPayoutAction(1); //Order Payout Action: 0 = Pending, 1 = Added to payout, 2 = Refunded
                            $vendorOrder->save();
                        }
                    }
                    $this->updateVendorPayoutHistory($payoutCallbackData, $status = 4);
                } else if ($payoutCallbackData['state'] == 'payment_deposited' || $payoutCallbackData['state'] == 'completed') {
                    $this->vendorOrderCollectionFactory->clear()->getSelect()->reset(\Zend_Db_Select::WHERE);
                    //$this->vendorOrderCollectionFactory->load();
                    $vendorOrderCollection = $this->vendorOrderCollectionFactory
                                    ->addFieldToFilter('id', ['in' => $payoutCallbackData['ext_info']['vendor_order_ids']])
                                    ->addFieldToFilter('vendor_id', $payoutCallbackData['ext_info']['vendor_id'])->load();

                    if (!empty($vendorOrderCollection)) {
                        /**
                         * payout_status = 2 and payout_action = 2 means Refunded
                         */
                        foreach ($vendorOrderCollection as $vendorOrder) {
                            $vendorOrder->setPayoutStatus(1); //Vendor Payout Status: 0 = Unpaid, 1 = Paid, 2 = Refund, 3 = In progress, 4 = Processed - awaiting settlement
                            $vendorOrder->setPayoutAction(1); //Order Payout Action: 0 = Pending, 1 = Added to payout, 2 = Refunded
                            $vendorOrder->save();
                        }
                        /*
                         * To do: Send invoice email to vendor
                         */
                        /* start send notification mail to MO */
                        $storeOwnerEmail = $this->_helper->getConfig('trans_email/ident_general/email');
                        $storeOwnername = $this->_helper->getConfig('trans_email/ident_general/name');
                        /* Receiver Detail  */
                        $receiverInfo = [
                            'name' => $storeOwnername,
                            'email' => $storeOwnerEmail
                        ];
                        /* Sender Detail  */
                        $senderInfo = array();
                        /* Assign values for your template variables  */
                        $emailTempVariables = array();
                        $emailTempVariables['amount'] = $this->_helper->formatToBaseCurrency($payoutCallbackData['amount']);
                        $emailTempVariables['reference_id'] = $payoutCallbackData['payout_ref'];
                        $emailTempVariables['payout_history_link'] = $this->_backendUrl->getUrl('omnyfy_mcm/payouthistory/index');


                        $emailTemplateId = $this->_helper->getPayoutNotificationTemplate();

                        if ($emailTemplateId) {
                            /* call send mail method from helper or where it define */
                            $this->_emailHelper->mcmMailSend(
                                $emailTemplateId, $emailTempVariables, $senderInfo, $receiverInfo
                            );
                        }
                        /* end send notification mail to MO */
                        /* start send notification mail to Vendor */
                        $vendorModel = $this->_vendorModel->load($payoutCallbackData['ext_info']['vendor_id']);

                        /* Receiver Detail  */
                        $vendorReceiverInfo = [
                            'name' => $vendorModel->getName(),
                            'email' => $vendorModel->getEmail()
                        ];
                        /* Sender Detail  */
                        $senderInfo = array();
                        /* Assign values for your template variables  */
                        $vendorEmailTempVariables = array();
                        $vendorEmailTempVariables['amount'] = $this->_helper->formatToBaseCurrency($payoutCallbackData['amount']);
                        $vendorEmailTempVariables['vendor_name'] = $vendorModel->getName();
                        $vendorEmailTempVariables['payout_history_link'] = $this->_backendUrl->getUrl('omnyfy_mcm/payouthistory/index');

                        /* call send mail method from helper or where it define */
                        $this->_emailHelper->mcmMailSend(
                                'payout_notification_to_vendor_email_template', $vendorEmailTempVariables, $senderInfo, $vendorReceiverInfo
                        );
                        /* end send notification mail to Vendor */
                    }
                    $this->updateVendorPayout($payoutCallbackData);
                    $this->updateVendorPayoutHistory($payoutCallbackData, $status = 1);
                }
            }
        }
    }

    protected function updateVendorPayoutHistory($payoutCallbackData, $status = 0) {
        $this->vendorPayoutHistoryCollectionFactory->clear()->getSelect()->reset(\Zend_Db_Select::WHERE);
        $vendorPayoutHistoryCollection = $this->vendorPayoutHistoryCollectionFactory
                ->addFieldToFilter('payout_ref', $payoutCallbackData['payout_ref'])
                ->addFieldToFilter('vendor_id', $payoutCallbackData['ext_info']['vendor_id'])
                ->addFieldToFilter('payout_id', $payoutCallbackData['ext_info']['payout_id'])
                ->addFieldToFilter('vendor_order_id', ['in' => $payoutCallbackData['ext_info']['vendor_order_ids']])->load();
        if (!empty($vendorPayoutHistoryCollection)) {
            foreach ($vendorPayoutHistoryCollection as $vendorPayoutHistory) {
                $vendorPayoutHistory->setStatus($status); //Payout Status: 0 = Failed, 1 = Success, 3 = In progress, 4 = Processed - awaiting settlement
                $vendorPayoutHistory->save();                
            }
        }
    }

    protected function updateVendorPayout($payoutCallbackData) {
        $payout = $this->vendorPayoutFactory->load($payoutCallbackData['ext_info']['payout_id']);
        $payout->setEwalletBalance($payout->getEwalletBalance() + $payoutCallbackData['amount']);
        $payout->save();
    }

}