<?php

namespace Omnyfy\Mcm\Plugin\Config;

use Omnyfy\Mcm\Helper\Data;
use Omnyfy\Vendor\Model\Vendor as VendorModel;
use Omnyfy\Mcm\Model\FeesCharges;
use Omnyfy\Mcm\Model\VendorPayout;
use Magento\Store\Model\StoreManagerInterface;

class ConfigPlugin {

    const MCM_SECTION = 'marketplacesetting';

    protected $feeChargesModel;
    protected $dataHelper;
    protected $storeManager;

    public function __construct(FeesCharges $feesChargesModel, Data $dataHelper, StoreManagerInterface $storeManager, VendorPayout $payoutModel, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Message\ManagerInterface $messageManager, \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory
    ) {
        $this->feeChargesModel = $feesChargesModel;
        $this->dataHelper = $dataHelper;
        $this->storeManager = $storeManager;
        $this->payoutModel = $payoutModel;
        $this->_logger = $logger;
        $this->messageManager = $messageManager;
        $this->vendorCollectionFactory = $vendorCollectionFactory;
    }

    public function aroundSave(
    \Magento\Config\Model\Config $subject, \Closure $proceed
    ) {
        if ($subject->getSection() == self::MCM_SECTION) {
            if ($subject['groups']['general']['fields']['fees_management']['value']) {
                $vendorCollection = $this->vendorCollectionFactory->create();
                if (!empty($vendorCollection)) {
                    $storeId = $this->storeManager->getStore()->getId();

                    foreach ($vendorCollection as $vendor) {
                        $feeChargesModel = NULL;
                        $feeChargesModel = $this->feeChargesModel->load($vendor->getEntityId(), 'vendor_id');

                        if ($vendor->getEntityId() != $feeChargesModel->getVendorId()) {
                            $sellerFeesEnable = $this->dataHelper->getGeneralConfig(Data::SELLER_FEES_ENABLE, $storeId);
                            $sellerFee = $sellerFeesEnable ? $this->dataHelper->getGeneralConfig(Data::DEFAULT_SELLER_FEES, $storeId) : 0;
                            $minSellerFee = $sellerFeesEnable ? $this->dataHelper->getGeneralConfig(Data::DEFAULT_MIN_SELLER_FEES, $storeId) : 0;
                            $maxSellerFee = $sellerFeesEnable ? $this->dataHelper->getGeneralConfig(Data::DEFAULT_MAX_SELLER_FEES, $storeId) : 0;
                            $disbursementFee = $sellerFeesEnable ? $this->dataHelper->getGeneralConfig(Data::DEFAULT_DISBURSMENT_FEES, $storeId) : 0;
                            $status = $sellerFeesEnable ? 1 : 0;
                            $feeChargesData = [
                                'vendor_id' => $vendor->getEntityId(),
                                'seller_fee' => $sellerFee,
                                'min_seller_fee' => $minSellerFee,
                                'max_seller_fee' => $maxSellerFee,
                                'disbursement_fee' => $disbursementFee,
                                'status' => $status
                            ];

                            $feeChargesModel->setData($feeChargesData);
                            $feeChargesModel->save();
                            $payoutModel = '';
                            $payoutModel = $this->payoutModel->load($vendor->getEntityId(), 'vendor_id');
                            if (($vendor->getEntityId() != $payoutModel->getVendorId()) && $feeChargesModel->getId()) {
                                $payoutData = [
                                    'fees_charges_id' => $feeChargesModel->getId(),
                                    'vendor_id' => $vendor->getEntityId(),
                                    'balance_owing' => 0,
                                    'payout_amount' => 0
                                ];
                                $payoutModel->setData($payoutData);
                                $payoutModel->save();
                            }
                        }
                    }
                }
            }
        }
        return $proceed();
    }

}
