<?php
/**
 * Project: Vendor SignUp
 * User: jing
 * Date: 4/9/19
 * Time: 12:30 pm
 */
namespace Omnyfy\VendorSignUp\Observer;

class AddKycButton implements \Magento\Framework\Event\ObserverInterface
{
    protected $registry;

    protected $urlBuilder;

    protected $backendHelper;

    protected $_config;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Omnyfy\VendorSignUp\Helper\Backend $backendHelper,
        \Omnyfy\Mcm\Model\Config $config
    ) {
        $this->registry = $registry;
        $this->urlBuilder = $urlBuilder;
        $this->backendHelper = $backendHelper;
        $this->_config = $config;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->_config->isIncludeKyc()) {
            return;
        }

        $buttonList = $observer->getData('button_list');

        //load current vendor from registry, do nothing if no current vendor set
        $vendor = $this->registry->registry('current_omnyfy_vendor_vendor');
        if (empty($vendor) || empty($vendor->getId())) {
            return;
        }

        //load kyc status by vendor id, if kyc status is pending, then show button.
        $kyc = $this->getKycByVendorId($vendor->getId());
        if (empty($kyc) || \Omnyfy\VendorSignUp\Model\Source\KycStatus::STATUS_PENDING != $kyc->getKycStatus() || empty($kyc->getSignupId())) {
            return;
        }

        $signUp = $this->backendHelper->getSignUpById($kyc->getSignupId());
        if (empty($signUp)) {
            return;
        }

        $bankAccount = $this->backendHelper->getBankAccountByVendorId($vendor->getId());
        if (empty($bankAccount)) {
            return;
        }

        //generate url with vendor id
        $url = $this->urlBuilder->getUrl('omnyfy_vendorsignup/kyc/send',
            [
                'vendor_id' => $vendor->getId(),
            ]
        );

        $buttonList->add(
            'send_kyc',
            [
                'label' => __('Send KYC'),
                'on_click' => sprintf("location.href = '%s';", $url),
            ]
        );
    }

    protected function getKycByVendorId($vendorId)
    {
        return $this->backendHelper->getKycByVendorId($vendorId);
    }
}
 
