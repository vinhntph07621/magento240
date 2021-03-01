<?php

/**
 * Sfmc get payload controller
 */

namespace Omnyfy\Mcm\Controller\Adminhtml\VendorWithdrawal;

use Magento\Framework\Controller\ResultFactory;

class GetBankDetail extends \Magento\Backend\App\Action {

    protected $dataPersistor;
    protected $_templateData;
    protected $_payloadArr;
    protected $_finalPayloadArr;

    protected $vendorBankAccountFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Omnyfy\Mcm\Model\VendorBankAccountFactory $vendorBankAccountFactory
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->vendorBankAccountFactory = $vendorBankAccountFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute() {
        try {
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $bankAccountId = $this->getRequest()->getPost('id');
            $bankAccountModel = '';
            if ($bankAccountId) {
                $bankAccountModel = $this->vendorBankAccountFactory->create()->load($bankAccountId);
                $bankAccountData = [
                    'account_name' => $bankAccountModel->getAccountName(),
                    'bank_name' => $bankAccountModel->getBankName(),
                    'bsb' => $bankAccountModel->getBsb(),
                    'account_number' => $bankAccountModel->getAccountNumber(),
                    'message' => 'Success'
                ];
                if (!empty($bankAccountData)) {
                    return $resultJson->setData($bankAccountData);
                }
            }
        } catch (\Exception $e) {
            return $resultJson->setData([
                        "message" => __('Error:%1', $e->getMessage()),
            ]);
        }
    }

}
