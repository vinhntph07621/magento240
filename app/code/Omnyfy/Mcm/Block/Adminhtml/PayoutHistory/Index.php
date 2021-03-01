<?php

namespace Omnyfy\Mcm\Block\Adminhtml\PayoutHistory;

use Omnyfy\Mcm\Model\ResourceModel\VendorPayoutHistory;
use Omnyfy\Mcm\Helper\Data as HelperData;

class Index extends \Magento\Backend\Block\Widget {

    protected $vendorPayoutHistoryResource;
    protected $pricing;

    protected $_helper;

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'payout_history/index.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        VendorPayoutHistory $vendorPayoutHistoryResource,
        \Magento\Framework\Pricing\Helper\Data $pricing,
        HelperData $helper,
        array $data = []
    ) {
        $this->vendorPayoutHistoryResource = $vendorPayoutHistoryResource;
        $this->pricing = $pricing;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getTotalPayouts() {
        $vendorId = $this->getVendorId();
        return $this->vendorPayoutHistoryResource->getTotalPayouts($vendorId);
    }

    public function currency($value) {
        return $this->_helper->formatToBaseCurrency($value);
    }

    public function getPayoutLastUpdated() {
        return $this->_localeDate->date(new \DateTime($this->vendorPayoutHistoryResource->getPayoutLastUpdated()))->format('h:i A');
    }

    public function getLastPayouts() {
        return $this->vendorPayoutHistoryResource->getLastPayouts();
    }

    public function getReadyToPayLastUpdated() {
        return $this->vendorPayoutHistoryResource->getReadyToPayLastUpdated();
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

}
