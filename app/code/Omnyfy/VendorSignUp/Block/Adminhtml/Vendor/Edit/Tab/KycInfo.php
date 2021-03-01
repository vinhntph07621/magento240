<?php
namespace Omnyfy\VendorSignUp\Block\Adminhtml\Vendor\Edit\Tab;

use \Magento\Backend\Block\Widget\Tab\TabInterface;
use Omnyfy\VendorSignUp\Model\VendorKyc;
use Omnyfy\VendorSignUp\Model\SignUp;
use Magento\Framework\App\Request\DataPersistorInterface;

class KycInfo extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface {
	
	//protected $_template = 'signup/scripts.phtml';
	protected $kycFactory;

	protected $signUpFactory;

	protected $countryCode;

	protected $countrySource;

	protected $dataPersistor;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Omnyfy\VendorSignUp\Model\VendorKycFactory $vendorKycFactory,
        \Omnyfy\VendorSignUp\Model\SignUpFactory $signUpFactory,
        \Omnyfy\VendorSignUp\Model\Source\CountryCode $countryCode,
        \Magento\Directory\Model\Config\Source\Country $countrySource,
        DataPersistorInterface $dataPersistor,
        array $data = []
    ) {
        $this->kycFactory = $vendorKycFactory;
        $this->signUpFactory = $signUpFactory;
        $this->countryCode = $countryCode;
		$this->countrySource = $countrySource;
		$this->dataPersistor = $dataPersistor;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel() {
        return 'Personal Information';
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle() {
        return 'Personal Information';
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

    protected function _prepareForm() {
        $model = $this->_coreRegistry->registry('current_omnyfy_vendor_vendor');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('vendor_');

        $kyc = $this->kycFactory->create();
        $kyc->load($model->getId(), 'vendor_id');

        $signUp = $this->signUpFactory->create();
        $signUp->load($kyc->getSignupId());

        if (!empty($signUp->getId())) {
            $model->setData('first_name', $signUp->getFirstName());
            $model->setData('last_name', $signUp->getLastName());
            $model->setData('dob', $signUp->getDob());
            $model->setData('business_address', $signUp->getBusinessAddress());
            $model->setData('city', $signUp->getCity());
            $model->setData('state', $signUp->getState());
            $model->setData('country', $signUp->getCountry());
            $model->setData('postcode', $signUp->getPostcode());
            $model->setData('country_code', $signUp->getCountryCode());
            $model->setData('telephone', $signUp->getTelephone());
            $model->setData('kyc_email', $signUp->getEmail());
            $model->setData('legal_entity', $signUp->getLegalEntity());
            $model->setData('tax_number', $signUp->getTaxNumber());
            $model->setData('abn', $signUp->getAbn());
        }
        $fieldset = $form->addFieldset('vendorsignup_kyc_info', ['legend' => __('Personal Information')]);

        $fieldset->addField(
                'first_name', 'text', [
				'name' => 'first_name',
				'label' => __('First Name'),
				'title' => __('First Name'),
				'required' => true,
				'maxlength' => 100,
				'sortOrder' => 20,
                ]
        );
		
        $fieldset->addField(
                'last_name', 'text', [
				'name' => 'last_name',
				'label' => __('Last Name'),
				'title' => __('Last Name'),
				'required' => true,
				'maxlength' => 100,
				'sortOrder' => 30,
                ]
        );
		$fieldset->addField(
            'dob',
            'date',
            [
                'name' => 'dob',
                'label' => __('Date of Birth'),
				'required' => true,
                'date_format' => 'yyyy-MM-dd'
            ]
        );
		
		$fieldset->addField(
                'business_address', 'text', [
				'name' => 'business_address',
				'label' => __('Business Address'),
				'title' => __('Business Address'),
				'required' => true,
				'sortOrder' => 40,
				'maxlength' => 200,
			]
        );
		
        $fieldset->addField(
                'city', 'text', [
				'name' => 'city',
				'label' => __('City'),
				'title' => __('City'),
				'required' => true,
				'maxlength' => 100,
				'sortOrder' => 50,
                ]
        );
		
        $fieldset->addField(
                'state', 'text', [
				'name' => 'state',
				'label' => __('State'),
				'title' => __('State'),
				'required' => true,
				'maxlength' => 100,
				'sortOrder' => 60,
                ]
        );
		
		$optionsc = $this->countrySource->toOptionArray();
		
        $fieldset->addField(
                'country', 'select', [
				'name' => 'country',
				'label' => __('Country'),
				'title' => __('Country'),
				'required' => true,
				'values' => $optionsc,
				'sortOrder' => 70,
                ]
        );
		
        $fieldset->addField(
                'postcode', 'text', [
				'name' => 'postcode',
				'label' => __('Postcode'),
				'title' => __('Postcode'),
				'required' => true,
				'maxlength' => 100,
				'sortOrder' => 80,
                ]
        );
		
        $fieldset->addField(
                'country_code', 'select', [
				'name' => 'country_code',
				'label' => __('Country Code'),
				'title' => __('Country Code'),
				'required' => true,
				'options' => $this->countryCode->_toArray(),
				'sortOrder' => 90,
                ]
        );
		
        $fieldset->addField(
                'telephone', 'text', [
				'name' => 'telephone',
				'label' => __('Telephone'),
				'title' => __('Telephone'),
				'required' => true,
				'maxlength' => 100,
				'sortOrder' => 100,
                ]
        );

        $fieldset->addField(
                'legal_entity', 'text', [
				'name' => 'legal_entity',
				'label' => __('Legal Entity Name'),
				'title' => __('Legal Entity Name'),
				'required' => true,
				'sortOrder' => 120,
                ]
        );

        $fieldset->addField(
                'tax_number', 'select', [
				'name' => 'tax_number',
				'class' => 'country-check-tax',
				'label' => __('Tax Name'),
				'title' => __('Tax Name'),
				'required' => true,
				'sortOrder' => 130,
                ]
        );
		
        $fieldset->addField(
                'abn', 'text', [
				'name' => 'abn',
				'label' => __('Tax Number'),
				'title' => __('Tax Number'),
				'required' => true,
				'maxlength' => 100,
				'sortOrder' => 140,
                ]
        );
		
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
