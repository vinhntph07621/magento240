<?php

namespace Omnyfy\Mcm\Block\Adminhtml\VendorWithdrawal;

use Omnyfy\Mcm\Model\VendorWithdrawalHistory;
use Omnyfy\Mcm\Model\VendorBankAccount;
use Omnyfy\Mcm\Helper\Data as HelperData;

class View extends \Magento\Backend\Block\Widget {

    protected $vendorPayoutResource;
    protected $pricing;

    protected $_helper;

    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'vendor_withdrawal/view.phtml';

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Pricing\Helper\Data $pricing,
        VendorWithdrawalHistory $vendorWithdrawalHistory,
        VendorBankAccount $vendorBankAccount,
        HelperData $helper,
        array $data = []
    ) {
        $this->pricing = $pricing;
        $this->vendorWithdrawalHistory = $vendorWithdrawalHistory;
        $this->vendorBankAccount = $vendorBankAccount;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }

    public function getVendorWithdrawalHistory() {
        if ($this->getWithdrawalId()) {
            $withdrawal = $this->vendorWithdrawalHistory->load($this->getWithdrawalId());
            if (!empty($withdrawal->getData())) {
                return $withdrawal->getData();
            }
        }
        return;
    }

    public function getVendorBankAccount() {
        if ($this->getVendorWithdrawalHistory()) {
            $bankAccount = $this->getVendorWithdrawalHistory();
            if (isset($bankAccount['bank_account_id'])) {
                $bankInfo = $this->vendorBankAccount->load($bankAccount['bank_account_id']);
                if (!empty($bankInfo->getData())) {
                    return $bankInfo->getData();
                }
            }
        }
        return;
    }

    public function getDateWithFormat($date, $format = 'Y-m-d H:i:s') {
        return $date ? $this->_localeDate->date(new \DateTime($date))->format($format) : '';
    }

    public function currency($value) {
        return $this->_helper->formatToBaseCurrency($value);
    }

    public function getWithdrawalId() {
        $withdrawalId = $this->getRequest()->getParam('id');
        return $withdrawalId;
    }

    public function getWithdrawalLastUpdated() {
        $vendorId = $this->getVendorId();
        $time = $this->vendorPayoutHistoryResource->getWithdrawalLastUpdated($vendorId);
        return $time ? $this->_localeDate->date(new \DateTime($time))->format('h:i A') : '';
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl() {
        return $this->getUrl('*/*/');
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

    protected function _prepareLayout() {
        $this->getToolbar()->addChild(
                'back', 'Magento\Backend\Block\Widget\Button', [
            'label' => __('Back'),
            'data_attribute' => [
                'role' => 'back',
            ],
            'class' => 'action-default scalable back',
            'onclick' => sprintf("location.href = '%s';", $this->getBackUrl()),
                ]
        );
        return parent::_prepareLayout();
    }
    
}
