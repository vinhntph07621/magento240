<?php

namespace Omnyfy\Mcm\Block\Adminhtml\VendorWithdrawal;

use Omnyfy\Mcm\Model\ResourceModel\VendorPayout;
use Omnyfy\Mcm\Model\ResourceModel\VendorPayoutHistory;
use Omnyfy\Mcm\Helper\Data as HelperData;

class Index extends \Magento\Backend\Block\Widget {

    protected $vendorPayoutResource;
    protected $pricing;

    protected $vendorPayoutHistoryResource;

    protected $_helper;

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'vendor_withdrawal/index.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        VendorPayout $vendorPayoutResource,
        \Magento\Framework\Pricing\Helper\Data $pricing,
        VendorPayoutHistory $vendorPayoutHistory,
        HelperData $helper,
        array $data = []
    ) {
        $this->vendorPayoutResource = $vendorPayoutResource;
        $this->pricing = $pricing;
        $this->vendorPayoutHistoryResource = $vendorPayoutHistory;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getTotalEarning() {
        $vendorId = $this->getVendorId();
        return $this->vendorPayoutResource->getTotalEarning($vendorId);
    }

    public function currency($value) {
        return $this->_helper->formatToBaseCurrency($value);
    }

    public function getEarningLastUpdated() {
        $vendorId = $this->getVendorId();
        $time = $this->vendorPayoutResource->getEarningLastUpdated($vendorId);
        return $time ? $this->_localeDate->date(new \DateTime($time))->format('h:i A') : '';
    }

    public function getTotalEarningCurrentMonth() {
        $vendorId = $this->getVendorId();
        return $this->vendorPayoutResource->getTotalEarningCurrentMonth($vendorId);
    }

    public function getVendorId() {
        $vendorId = '';
        $vendorId = $this->getRequest()->getParam('vendor_id');
        if (!$vendorId) {
            $vendorInfo = $this->_backendSession->getVendorInfo();
            if (!empty($vendorInfo) && isset($vendorInfo['vendor_id'])) {
                $vendorId = $vendorInfo['vendor_id'];
            }
        }
        return $vendorId;
    }

    public function getWithdrawalsPayoutReceived() {
        $vendorId = $this->getVendorId();
        return $this->vendorPayoutHistoryResource->payoutReceived($vendorId);
    }
    
    public function getWithdrawalAmount(){
        $vendorId = $this->getVendorId();
        return $this->vendorPayoutHistoryResource->getWithdrawalAmount($vendorId);
    }

    public function ewalletAvailableBalance() {
        $vendorId = $this->getVendorId();
        return $this->vendorPayoutHistoryResource->ewalletAvailableBalance($vendorId);
    }
    
    public function getWithdrawalLastUpdated() {
        $vendorId = $this->getVendorId();
        $time = $this->vendorPayoutHistoryResource->getWithdrawalLastUpdated($vendorId);
        return $time ? $this->_localeDate->date(new \DateTime($time))->format('h:i A') : '';
    }
    
}
