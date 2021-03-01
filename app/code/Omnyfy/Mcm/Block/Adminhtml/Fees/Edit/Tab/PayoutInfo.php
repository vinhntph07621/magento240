<?php

namespace Omnyfy\Mcm\Block\Adminhtml\Fees\Edit\Tab;
use Omnyfy\Mcm\Model\VendorPayoutFactory;

class PayoutInfo extends \Magento\Backend\Block\Widget {

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'fees/payout_info.phtml';

    public function __construct(
    \Magento\Backend\Block\Template\Context $context, VendorPayoutFactory $vendorPayoutFactory, array $data = []
    ) {
        $this->vendorPayoutFactory = $vendorPayoutFactory->create();
        parent::__construct($context, $data);
    }
    
    public function getVendorEwalletId(){
        $feeId = $this->getRequest()->getParam('id');
        $vendorPayout = $this->vendorPayoutFactory->load($feeId, 'fees_charges_id');
        return $vendorPayout->getEwalletId();
    }
}