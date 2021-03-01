<?php

namespace Omnyfy\Mcm\Block\Adminhtml\VendorWithdrawal;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class BackButton
 */
class SubmitButton implements ButtonProviderInterface {

    /**
     * @var Context
     */
    protected $context;
    protected $adminSession;

    public function __construct(
        Context $context
    ) {
        $this->context = $context;
        $this->adminSession = $context->getBackendSession();
    }

    /**
     * @return array
     */
    public function getButtonData() {
        if ($this->getVendorId()) {
            return [
                'label' => __('Submit'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 90,
            ];
        }
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getSubmitUrl() {
        return $this->getUrl('*/*/save', ['vendor_id' => $this->getVendorId()]);
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = []) {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }

    public function getVendorId() {
        $vendorId = '';
        $vendorId = $this->context->getRequest()->getParam('vendor_id');
        if (!$vendorId) {
            $vendorInfo = $this->adminSession->getVendorInfo();
            if (!empty($vendorInfo) && isset($vendorInfo['vendor_id'])) {
                $vendorId = $vendorInfo['vendor_id'];
            }
        }
        return $vendorId;
    }

}
