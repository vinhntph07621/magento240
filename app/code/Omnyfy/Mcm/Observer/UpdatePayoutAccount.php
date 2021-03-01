<?php
/**
 * Project: MCM
 * User: jing
 * Date: 24/1/20
 * Time: 3:25 pm
 */
namespace Omnyfy\Mcm\Observer;

class UpdatePayoutAccount implements \Magento\Framework\Event\ObserverInterface
{
    protected $_vendorPayoutResource;

    protected $helper;

    protected $feesChargesFactory;

    protected $payoutFactory;

    protected $_logger;

    public function __construct(
        \Omnyfy\Mcm\Model\ResourceModel\VendorPayout $vendorPayoutResource,
        \Omnyfy\Mcm\Helper\Data $helper,
        \Omnyfy\Mcm\Model\FeesChargesFactory $feesChargesFactory,
        \Omnyfy\Mcm\Model\VendorPayoutFactory $payoutFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->_vendorPayoutResource = $vendorPayoutResource;
        $this->helper = $helper;
        $this->feesChargesFactory = $feesChargesFactory;
        $this->payoutFactory = $payoutFactory;
        $this->_logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $vendorId = $observer->getData('vendor_id');
        $accountRef = $observer->getData('account_ref');

        if ($this->helper->getGeneralConfig(\Omnyfy\Mcm\Helper\Data::MCM_ENABLE))
        {
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
                $this->_logger->debug('fee save: ', $feeChargesData);
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
                $this->_logger->debug('payout save:', $payoutData);
            }
        }

        $this->_logger->debug('update account: '. $vendorId. ',' . $accountRef);
        $this->_vendorPayoutResource->updateAccountRef($vendorId, $accountRef);
    }
}
 