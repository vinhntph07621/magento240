<?php

namespace Omnyfy\Mcm\Block\Adminhtml\PendingPayouts\View\Tab;

use Omnyfy\Mcm\Model\VendorPayoutFactory;
use Omnyfy\Mcm\Model\ResourceModel\VendorPayout;
use Magento\Framework\Pricing\Helper\Data;
use Omnyfy\Mcm\Helper\Data as HelperData;

class PendingOrders extends \Magento\Backend\Block\Widget {

    protected $vendorPayoutFactory;
    protected $vendorPayoutResource;
    protected $pricing;

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'pending_payouts/view/tab/info.phtml';

    public function __construct(
    \Magento\Backend\Block\Template\Context $context, VendorPayoutFactory $vendorPayoutFactory, VendorPayout $vendorPayoutResource, Data $pricing, HelperData $helper, array $data = []
    ) {
        $this->vendorPayoutFactory = $vendorPayoutFactory->create();
        $this->vendorPayoutResource = $vendorPayoutResource;
        $this->pricing = $pricing;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getTotalPendingPayoutOrder() {
        $vendorId = $this->getRequest()->getParam('vendor_id');
        return $this->vendorPayoutResource->getTotalPendingPayoutOrder($vendorId);
    }

    public function getTotalPayoutAmount() {
        $total = $this->getTotalPendingPayoutOrder();
        if (!empty($total)) {
            return $this->currency($total['total_payout_amount']);
        }
        return $this->currency(0);
    }

    public function getTotalFeesCharged() {
        $total = $this->getTotalPendingPayoutOrder();
        if (!empty($total)) {
            return $this->currency($total['total_fees_paid_incl_tax']);
        }
        return $this->currency(0);
    }

    public function currency($value) {
        return $this->_helper->formatToBaseCurrency($value);
    }
}
