<?php

namespace Omnyfy\Mcm\Block\Adminhtml\MarketplaceEarnings;

use Omnyfy\Mcm\Model\ResourceModel\VendorPayout;
use Omnyfy\Mcm\Model\ResourceModel\VendorPayoutHistory;
use Magento\Backend\Model\UrlInterface;
use Omnyfy\Mcm\Helper\Data as HelperData;

class Index extends \Magento\Backend\Block\Widget {

    protected $vendorPayoutResource;
    protected $pricing;

    protected $_helper;

    protected $_backendUrl;

    protected $vendorPayoutHistoryResource;
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'marketplace_earnings/index.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        VendorPayout $vendorPayoutResource,
        \Magento\Framework\Pricing\Helper\Data $pricing,
        VendorPayoutHistory $vendorPayoutHistory,
        UrlInterface $backendUrl,
        HelperData $helper,
        array $data = []
    ) {
        $this->vendorPayoutResource = $vendorPayoutResource;
        $this->pricing = $pricing;
        $this->vendorPayoutHistoryResource = $vendorPayoutHistory;
        $this->_backendUrl = $backendUrl;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getTotalEarning() {
        return $this->vendorPayoutResource->getTotalMarketplaceEarning();
    }

    public function currency($value) {
        return $this->_helper->formatToBaseCurrency($value);
    }

    public function getEarningLastUpdated() {
        $time = $this->vendorPayoutResource->getEarningLastUpdated();
        return $time ? $this->_localeDate->date(new \DateTime($time))->format('h:i A') : '';
    }

    public function getTotalEarningCurrentMonth() {
        return $this->vendorPayoutResource->getTotalMarketplaceEarningCurrentMonth();
    }

    public function getWithdrawalsPayoutReceived() {
        return $this->vendorPayoutHistoryResource->payoutReceived();
    }

    public function getWithdrawalAmount() {
        return $this->vendorPayoutHistoryResource->getWithdrawalAmount();
    }

    public function ewalletAvailableBalance() {
        return $this->vendorPayoutHistoryResource->ewalletAvailableBalance();
    }

    public function getWithdrawalLastUpdated() {
        $time = $this->vendorPayoutHistoryResource->getWithdrawalLastUpdated();
        return $time ? $this->_localeDate->date(new \DateTime($time))->format('h:i A') : '';
    }

    public function getVendorWithdrawalUrl() {
        if ($this->getVendorId()) {
            return $this->_backendUrl->getUrl('omnyfy_mcm/vendorWithdrawal/index/vendor_id/' . $this->getVendorId());
        } else {
            return $this->_backendUrl->getUrl('omnyfy_mcm/vendorWithdrawal/index');
        }
    }

}
