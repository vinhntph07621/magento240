<?php
namespace Omnyfy\Stripe\Block\Adminhtml\Vendor\Edit\Tab;

use \Magento\Backend\Block\Widget\Tab\TabInterface;

class CreateStripeAccount extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface {

    const STRIPE_URL = 'https://connect.stripe.com/express/oauth/authorize';

    const STRIPE_DASHBOARD_URL = 'https://dashboard.stripe.com';

    /**
     * @var \Omnyfy\Stripe\Helper\GetConfigData
     */
    protected $_configData;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlInterface;

    /**
     * @var \Omnyfy\Stripe\Helper\Gateway
     */
    protected $gatewayHelper;

    /**
     * CreateStripeAccount constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Omnyfy\Stripe\Helper\GetConfigData $configData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Omnyfy\Stripe\Helper\GetConfigData $configData,
        \Omnyfy\Stripe\Helper\Gateway $gatewayHelper,
        array $data = []
    ) {
        $this->_configData = $configData;
        $this->_urlInterface = $context->getUrlBuilder();
        $this->gatewayHelper = $gatewayHelper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel() {
        return 'Payout and Banking';
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle() {
        return 'Payout and Banking';
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab() {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden() {
        return false;
    }

    /**
     * @return \Magento\Backend\Block\Widget\Form\Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm() {
        $vendor = $this->_coreRegistry->registry('current_omnyfy_vendor_vendor');
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('vendorsignup_stripe_account', ['legend' => __('Payout and Banking')]);

        $urlInterface = $this->_urlInterface;
        $vendorListUrl = $urlInterface->getUrl('omnyfy_vendor/vendor/');

        $clientId = $this->_configData->getClientId();
        $emailParam = !empty($vendor->getEmail()) ? "&stripe_user[email]=" . $vendor->getEmail() : '';

        $redirectLink = self::STRIPE_URL .
            "?redirect_uri=" . $vendorListUrl .
            "&client_id=" . $clientId .
            $emailParam;
        $buttonLabel = __('Create Stripe account');
        if (!empty($stripeAccountCode = $vendor->getStripeAccountCode())) {
            $descText = __("<p>Congratulations – you have successfully completed entering the information to complete the KYC (Know Your Customer) procedure and have registered your bank account to receive payments.</p><p>To change information in relation to your bank account and KYC information, please login to the Stripe Account that has been created for you.</p><p>You can also view your pending pay-out amounts and access your funds via your Stripe account.</p>");
            $fieldset->addField('vendor_stripe_description', 'note', ['text' => $descText]);
            $buttonLabel = __('Click here to login to your Stripe Account');
            $redirectLink = $this->gatewayHelper->getAccountLoginLink($stripeAccountCode);
        }
        $fieldset->addField(
            'redirectToStripe', 'button', [
                'value' => $buttonLabel,
                'onclick' =>
                    "window.open('" . $redirectLink . "')"
            ]
        );

        if (!empty($stripeAccountCode = $vendor->getStripeAccountCode())) {
            $stripeAccountData = $this->gatewayHelper->getUserById($vendor->getStripeAccountCode());
            $stripeAccountName = !empty($stripeAccountData['business_profile']['name']) ? $stripeAccountData['business_profile']['name'] : $stripeAccountData['email'];
            $fieldset->addField('vendor_stripe_account', 'note', ['label' => __('Stripe Account Name'), 'text' => $stripeAccountName]);
            $fieldset->addField('vendor_stripe_payout_status', 'note', ['label' => __('Payout Status'), 'text' => $this->getPayoutStatus($stripeAccountData)]);
            $fieldset->addField('vendor_stripe_transfer_status', 'note', ['label' => __('Transfer Status'), 'text' => $this->getTransferStatus($stripeAccountData)]);
        }

        $this->setForm($form);

        return parent::_prepareForm();
    }

    private function getPayoutStatus($data)
    {
        if (empty($data['payouts_enabled'])) {
            return __("Restricted");
        }
        return __("Enabled");
    }

    private function getTransferStatus($data)
    {
        if (empty($data['capabilities']['transfers'])) {
            return __("Restricted");
        }
        if ($data['capabilities']['transfers'] == 'active') {
            return __("Enabled");
        }
        return __("Restricted");
    }
}
