<?php

namespace Omnyfy\Mcm\Block\Adminhtml\VendorWithdrawal;

use Omnyfy\Mcm\Model\VendorWithdrawalHistory;
use Omnyfy\Mcm\Model\VendorBankAccount;
use Omnyfy\Mcm\Model\ResourceModel\VendorPayoutHistory;
use Omnyfy\Mcm\Helper\Data as HelperData;

class NewWithdrawal extends \Magento\Backend\Block\Widget {

    protected $vendorPayoutResource;
    protected $pricing;

    protected $_helper;

    protected $vendorPayoutHistoryResource;

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'vendor_withdrawal/new_withdrawal.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Pricing\Helper\Data $pricing,
        VendorWithdrawalHistory $vendorWithdrawalHistory,
        VendorBankAccount $vendorBankAccount,
        VendorPayoutHistory $vendorPayoutHistory,
        HelperData $helper,
        array $data = []
    ) {
        $this->pricing = $pricing;
        $this->vendorWithdrawalHistory = $vendorWithdrawalHistory;
        $this->vendorBankAccount = $vendorBankAccount;
        $this->vendorPayoutHistoryResource = $vendorPayoutHistory;
        $this->_helper = $helper;
        parent::__construct($context, $data);
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

    /**
     * Get URL for Vendor Bank Url
     *
     * @return string
     */
    public function getVendorBankUrl() {
        if ($this->getVendorId()) {
            return $this->getUrl('omnyfy_vendor/vendor/edit/', ['id' => $this->getVendorId(), 'active_tab' => 'mcm_bank_account_info']);
        }
        return;
    }

    /**
     * Get URL for Vendor Bank Account Detail Url
     *
     * @return string
     */
    public function getVendorBankAccDetailAjaxUrl() {
        return $this->getUrl('omnyfy_mcm/vendorWithdrawal/getBankDetail/');
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = []) {
        return $this->_urlBuilder->getUrl($route, $params);
    }
    
    public function currency($value) {
        return $this->_helper->formatToBaseCurrency($value);
    }
    
    public function ewalletAvailableBalance() {
        $vendorId = $this->getVendorId();
        return $this->vendorPayoutHistoryResource->ewalletAvailableBalance($vendorId);
    }
    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId() {
        return $this->_storeManager->getStore()->getId();
    }

}
