<?php
/**
 * Project: MCM
 * User: jing
 * Date: 2019-05-31
 * Time: 14:50
 */
namespace Omnyfy\Mcm\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

class VendorFormSave implements ObserverInterface
{
    protected $bankAccountFactory;

    protected $feesChargesFactory;

    protected $payoutFactory;

    protected $helper;

    protected $logger;

    protected $_config;

    protected $fieldsToCheck = [
        'account_type_id',
        'acc_country',
        'account_name',
        'bank_name',
        'bsb',
        'account_number',
        'account_type',
        'holder_type',
        'bank_address',
        'swift_code'
    ];

    public function __construct(
        \Omnyfy\Mcm\Model\VendorBankAccountFactory $bankAccountFactory,
        \Omnyfy\Mcm\Model\FeesChargesFactory $feesChargesFactory,
        \Omnyfy\Mcm\Model\VendorPayoutFactory $payoutFactory,
        \Omnyfy\Mcm\Helper\Data $helper,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Mcm\Model\Config $config
    ) {
        $this->bankAccountFactory = $bankAccountFactory;
        $this->feesChargesFactory = $feesChargesFactory;
        $this->payoutFactory = $payoutFactory;
        $this->helper = $helper;
        $this->logger = $logger;
        $this->_config = $config;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_config->isIncludeKyc()) {
            return;
        }

        $vendor = $observer->getData('vendor');
        $formData = $observer->getData('form_data');
        $isNew = $observer->getData('is_new');

        $vendorId = $vendor->getEntityId();

        $isChanged = false;
        if (!$isNew) {
            $vendorData = $vendor->getOrigData();
            foreach($this->fieldsToCheck as $field) {
                if ((!array_key_exists($field, $vendorData) || empty($vendorData[$field]))
                    && (!array_key_exists($field, $formData) || empty($formData[$field]))) {
                    continue;
                }
                if (!(isset($vendorData[$field]) && isset($formData[$field]) && $vendorData[$field]== $formData[$field])) {
                    $isChanged = true;
                    break;
                }
            }
        }

        if ($isNew || $isChanged) {
            $bankAccount = $this->bankAccountFactory->create();

            $data = $formData;
            if ($vendorId) {
                $bankAccount->load($vendorId, 'vendor_id');
                $data['id'] = $bankAccount->getId();
                $data['vendor_id'] = $vendorId;
            }
            $data['country'] = $data['acc_country'];
            $bankAccount->addData($data);
            $bankAccount->save();
        }

        if ($this->helper->getGeneralConfig(\Omnyfy\Mcm\Helper\Data::MCM_ENABLE)) {
            $fee = $this->feesChargesFactory->create()->load($vendorId, 'vendor_id');
            if (empty($fee->getId())) {
                $sellerFeesEnable = $this->helper->getGeneralConfig(\Omnyfy\Mcm\Helper\Data::SELLER_FEES_ENABLE);
                $sellerFee = $sellerFeesEnable ? $this->helper->getGeneralConfig(\Omnyfy\Mcm\Helper\Data::DEFAULT_SELLER_FEES) : 0;
                $minSellerFee = $sellerFeesEnable ? $this->helper->getGeneralConfig(\Omnyfy\Mcm\Helper\Data::DEFAULT_MIN_SELLER_FEES) : 0;
                $maxSellerFee = $sellerFeesEnable ? $this->helper->getGeneralConfig(\Omnyfy\Mcm\Helper\Data::DEFAULT_MAX_SELLER_FEES) : 0;
                $disbursementFee = $sellerFeesEnable ? $this->helper->getGeneralConfig(\Omnyfy\Mcm\Helper\Data::DEFAULT_DISBURSMENT_FEES) : 0;
                //$status = $sellerFeesEnable ? 1 : 0;
                $status = 0; // By default set In active
                $feeChargesData = [
                    'vendor_id' => $vendorId,
                    'seller_fee' => $sellerFee,
                    'min_seller_fee' => $minSellerFee,
                    'max_seller_fee' => $maxSellerFee,
                    'disbursement_fee' => $disbursementFee,
                    'status' => $status
                ];

                $fee->setData($feeChargesData);
                $fee->save();
            }

            $payout = $this->payoutFactory->create();
            $payout = $payout->load($vendorId, 'vendor_id');

            if (empty($payout->getId()) && $fee->getId()) {
                $payoutData = [
                    'fees_charges_id' => $fee->getId(),
                    'vendor_id' => $vendorId,
                    'balance_owing' => 0,
                    'payout_amount' => 0
                ];
                $payout->setData($payoutData);
                $payout->save();
            }
        }
    }
}
 