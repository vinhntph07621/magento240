<?php

namespace Omnyfy\Mcm\Block\Adminhtml\PendingPayouts;

use Omnyfy\Mcm\Model\ResourceModel\VendorPayout;
use Omnyfy\Mcm\Helper\Data as HelperData;

class Index extends \Magento\Backend\Block\Widget {

    protected $vendorPayoutResource;
    protected $pricing;

    protected $_helper;

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'pending_payouts/index.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        VendorPayout $vendorPayoutResource,
        \Magento\Framework\Pricing\Helper\Data $pricing,
        HelperData $helper,
        array $data = []
    ) {
        $this->vendorPayoutResource = $vendorPayoutResource;
        $this->pricing = $pricing;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getTotalPayoutsPending() {
        return $this->vendorPayoutResource->getTotalPayoutsPending();
    }

    public function currency($value) {
        return $this->_helper->formatToBaseCurrency($value);
    }
    public function getPendingPayoutLastUpdated(){
        return $this->_localeDate->date(new \DateTime($this->vendorPayoutResource->getPendingPayoutLastUpdated()))->format('h:i A');
    }
    
    public function getTotalReadyToPay() {
        return $this->vendorPayoutResource->getTotalReadyToPay();
    }
    
    public function getReadyToPayLastUpdated(){
        return $this->vendorPayoutResource->getReadyToPayLastUpdated();
    }
    
}
