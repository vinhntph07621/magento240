<?php

namespace Omnyfy\Mcm\Plugin\Vendor\Model;

use Omnyfy\Mcm\Helper\Data;
use Omnyfy\Vendor\Model\Vendor as VendorModel;
use Omnyfy\Mcm\Model\FeesCharges;
use Omnyfy\Mcm\Model\VendorPayout;
use Magento\Store\Model\StoreManagerInterface;
use Omnyfy\Mcm\Model\VendorBankAccount;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Response\Http as ResponseHttp;

class Vendor {

    protected $newVendor;
    protected $feeChargesModel;
    protected $dataHelper;
    protected $storeManager;

    public function __construct(FeesCharges $feesChargesModel, Data $dataHelper, StoreManagerInterface $storeManager, VendorPayout $payoutModel, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Message\ManagerInterface $messageManager, \Magento\Framework\App\RequestInterface $request, VendorBankAccount $vendorBankAccount, ResultFactory $resultFactory, RedirectInterface $redirect, DataPersistorInterface $dataPersistor, UrlInterface $url, ResponseHttp $response
    ) {
        $this->newVendor = 0;
        $this->feeChargesModel = $feesChargesModel;
        $this->dataHelper = $dataHelper;
        $this->storeManager = $storeManager;
        $this->payoutModel = $payoutModel;
        $this->_logger = $logger;
        $this->messageManager = $messageManager;
        $this->_request = $request;
        $this->vendorBankAccount = $vendorBankAccount;
        $this->resultFactory = $resultFactory;
        $this->redirect = $redirect;
        $this->dataPersistor = $dataPersistor;
        $this->_url = $url;
        $this->response = $response;
    }

    public function beforeSave(VendorModel $subject) {
        if ($subject->isObjectNew()) {
            $this->newVendor = 1;
        }
    }

    public function afterSave(VendorModel $subject) {
        try {
            $request = $this->_request;
            $id = '';
            $vendorId = '';
            if ($request->isPost()) {
                $data = $request->getPostValue();
                $this->dataPersistor->set('vendor_bank_acc', $data);
                $this->validateBankAccount($data);
                $vendorBankAccountModel = $this->vendorBankAccount;
                try {
                    $inputFilter = new \Zend_Filter_Input(
                            [], [], $data
                    );
                    $data = $inputFilter->getUnescaped();
                    $vendorId = $subject->getEntityId();

                    if ($vendorId) {
                        $vendorBankAccountModel = $vendorBankAccountModel->load($vendorId, 'vendor_id');
                        $data['id'] = $vendorBankAccountModel->getId();
                        $data['vendor_id'] = $vendorId;
                    }
                    $data['country'] = $data['acc_country'];
                    $vendorBankAccountModel->setData($data);
                    $vendorBankAccountModel->save();
                } catch (LocalizedException $e) {
                    $this->messageManager->addError(
                            __('Something went wrong while saving the bank account data. Please review the error log.')
                    );
                    $this->_logger->critical($e->getMessage());
                } catch (\Exception $e) {
                    $this->messageManager->addError(
                            __('Something went wrong while saving the bank account data. Please review the error log.')
                    );
                    $this->_logger->critical($e->getMessage());
                }
            }


            $storeId = $this->storeManager->getStore()->getId();
            if ($this->newVendor && $this->dataHelper->getGeneralConfig(Data::MCM_ENABLE, $storeId)) {
                $this->feeChargesModel = $this->feeChargesModel->load($subject->getEntityId(), 'vendor_id');
                if (empty($this->feeChargesModel->getDataByVendorId())) {
                    $sellerFeesEnable = $this->dataHelper->getGeneralConfig(Data::SELLER_FEES_ENABLE, $storeId);
                    $sellerFee = $sellerFeesEnable ? $this->dataHelper->getGeneralConfig(Data::DEFAULT_SELLER_FEES, $storeId) : 0;
                    $minSellerFee = $sellerFeesEnable ? $this->dataHelper->getGeneralConfig(Data::DEFAULT_MIN_SELLER_FEES, $storeId) : 0;
                    $maxSellerFee = $sellerFeesEnable ? $this->dataHelper->getGeneralConfig(Data::DEFAULT_MAX_SELLER_FEES, $storeId) : 0;
                    $disbursementFee = $sellerFeesEnable ? $this->dataHelper->getGeneralConfig(Data::DEFAULT_DISBURSMENT_FEES, $storeId) : 0;
                    //$status = $sellerFeesEnable ? 1 : 0;
					$status = 0; // By default set In active
                    $feeChargesData = [
                        'vendor_id' => $subject->getEntityId(),
                        'seller_fee' => $sellerFee,
                        'min_seller_fee' => $minSellerFee,
                        'max_seller_fee' => $maxSellerFee,
                        'disbursement_fee' => $disbursementFee,
                        'status' => $status
                    ];

                    $this->feeChargesModel->setData($feeChargesData);
                    $this->feeChargesModel->save();
                    $this->payoutModel = $this->payoutModel->load($subject->getEntityId(), 'vendor_id');
                    if (empty($this->payoutModel->getDataByVendorId()) && $this->feeChargesModel->getId()) {
                        $payoutData = [
                            'fees_charges_id' => $this->feeChargesModel->getId(),
                            'vendor_id' => $subject->getEntityId(),
                            'balance_owing' => 0,
                            'payout_amount' => 0
                        ];
                        $this->payoutModel->setData($payoutData);
                        $this->payoutModel->save();
                    }
                }
            }
        } catch (LocalizedException $e) {
            $this->messageManager->addError(
                    __('Something went wrong while saving the fees and charges data. Please review the error log.')
            );
            $this->_logger->critical($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(
                    __('Something went wrong while saving the fees and charges data. Please review the error log.')
            );
            $this->_logger->critical($e->getMessage());
        }
    }

    public function validateBankAccount($data) {
        if (!isset($data['account_name']) || empty($data['account_name'])) {
            $this->messageManager->addError(
                    __('Please enter a Bank Account Name.')
            );
            $this->redirect($data);
            //throw new LocalizedException(__('Please enter a Bank Account Name.'));
        } else if (strlen($data['account_name']) > 100) {
            $this->messageManager->addError(
                    __('Please enter less or equal than 100 symbols in Bank Account Name.')
            );
            $this->redirect($data);
        }

        if (!isset($data['bsb']) || empty($data['bsb'])) {
            $this->messageManager->addError(__('Please enter a BSB.'));
            $this->redirect($data);
        } else if (strlen($data['bsb']) > 20) {
            $this->messageManager->addError(__('Please enter less or equal than 20 symbols in BSB.'));
            $this->redirect($data);
        } else if (!is_numeric($data['bsb'])) {
            $this->messageManager->addError(__('Please enter a valid number in BSB.'));
            $this->redirect($data);
        }

        if (!isset($data['account_number']) || empty($data['account_number'])) {
            $this->messageManager->addError(__('Please enter a Account Number.'));
            $this->redirect($data);
        } else if (strlen($data['account_number']) > 20) {
            $this->messageManager->addError(__('Please enter less or equal than 20 symbols in Account Number.'));
            $this->redirect($data);
        } else if (!is_numeric($data['account_number'])) {
            $this->messageManager->addError(__('Please enter a valid number in Account Number.'));
            $this->redirect($data);
        }


        if (!isset($data['account_type_id']) || empty($data['account_type_id'])) {
            $this->messageManager->addError(__('Please select a Account Type.'));
            $this->redirect($data);
        } else {
            if ($data['account_type_id'] == 2) {
                if (!isset($data['swift_code']) || empty($data['swift_code'])) {
                    $this->messageManager->addError(__('Please enter a SWIFT Code.'));
                    $this->redirect($data);
                } else if (strlen($data['swift_code']) > 30) {
                    $this->messageManager->addError(__('Please enter less or equal than 30 symbols in SWIFT Code.'));
                    $this->redirect($data);
                } else if (!ctype_alnum($data['swift_code'])) {
                    $this->messageManager->addError(__('Please use only letters (a-z or A-Z) or numbers (0-9) in this field. No spaces or other characters are allowed in SWIFT Code.'));
                    $this->redirect($data);
                }
            }
        }
        if (strlen($data['bank_name']) > 200) {
            $this->messageManager->addError(__('Please enter less or equal than 200 symbols in Bank Name.'));
            $this->redirect($data);
        }
        if (strlen($data['bank_address']) > 200) {
            $this->messageManager->addError(__('Please enter less or equal than 200 symbols in Bank Address.'));
            $this->redirect($data);
        }
//        if (strlen($data['company_name']) > 200) {
//            $this->messageManager->addError(__('Please enter less or equal than 200 symbols in Company Name.'));
//            $this->redirect($data);
//        }
    }

    public function redirect($data) {
        $url = $this->_url->getUrl('*/*/edit', ['id' => $data['entity_id'], 'active_tab' => 'mcm_bank_account_info']);
        $this->response->setRedirect($url);
        return;
    }

}
