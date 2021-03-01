<?php

namespace Omnyfy\Mcm\Block\Adminhtml\Vendor\Edit\Tab;

use \Magento\Backend\Block\Widget\Tab\TabInterface;

class BankAccountInfo extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
{
    protected $vendorBankAccountType;

    protected $vendorBankAccountFactory;

    protected $_countryFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Omnyfy\Mcm\Model\Source\VendorBankAccountType $vendorBankAccountType,
        \Omnyfy\Mcm\Model\VendorBankAccountFactory $vendorBankAccountFactory,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        array $data = []
    ) {
        $this->vendorBankAccountType = $vendorBankAccountType;
        $this->vendorBankAccountFactory = $vendorBankAccountFactory;
        $this->_countryFactory = $countryFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel() {
        return 'Bank Account Information';
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle() {
        return 'Bank Account Information';
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

        $vendorBankAccountModel = $this->vendorBankAccountFactory->create();
        $vendorBankAccountModel->load($model->getId(), 'vendor_id');
        if (!empty($vendorBankAccountModel->getId())) {
            $model->setData('bank_name', $vendorBankAccountModel->getBankName());
            $model->setData('account_type_id', $vendorBankAccountModel->getAccountTypeId());
            $model->setData('account_name', $vendorBankAccountModel->getAccountName());
            $model->setData('account_number', $vendorBankAccountModel->getAccountNumber());
            $model->setData('bsb', $vendorBankAccountModel->getBsb());
//            $model->setData('company_name', $vendorBankAccountModel->getCompanyName());
            $model->setData('bank_address', $vendorBankAccountModel->getBankAddress());
            $model->setData('swift_code', $vendorBankAccountModel->getSwiftCode());
            $model->setData('account_type', $vendorBankAccountModel->getAccountType());
            $model->setData('holder_type', $vendorBankAccountModel->getHolderType());
            $model->setData('acc_country', $vendorBankAccountModel->getCountry());
        }
        $fieldset = $form->addFieldset('mcm_bank_account_info', ['legend' => __('Bank Account Information')]);
        $fieldset->addField(
                'account_type_id', 'select', [
            'name' => 'account_type_id',
            'label' => __('International/Domestic'),
            'title' => __('International/Domestic'),
            'required' => true,
            'options' => $this->vendorBankAccountType->_toArray(),
            'sortOrder' => 10,
                ]
        );

        $fieldset->addField(
                'acc_country', 'select', [
            'name' => 'acc_country',
            'label' => __('Country'),
            'title' => __('Country'),
            'required' => true,
            'values' => $this->_countryFactory->toOptionArray(),
            'sortOrder' => 15,
                ]
        );

        $fieldset->addField(
                'account_name', 'text', [
            'name' => 'account_name',
            'label' => __('Account Name'),
            'title' => __('Account Name'),
            'required' => true,
            'maxlength' => 100,
            'sortOrder' => 20,
                ]
        );

        $fieldset->addField(
                'bank_name', 'text', [
            'name' => 'bank_name',
            'label' => __('Bank Name'),
            'title' => __('Bank Name'),
            'required' => true,
            'sortOrder' => 30,
            'maxlength' => 100,
                ]
        );
        $fieldset->addField(
                'bsb', 'text', [
            'name' => 'bsb',
            'label' => __('BSB/Routing Number'),
            'title' => __('BSB/Routing Number'),
            'required' => true,
            'sortOrder' => 40,
            'maxlength' => 20,
            'class' => 'validate-number',
            'note' => 'For Australia bank account, please enter 6-digit BSB number. For New Zealand bank account, please leave this field blank. For US bank account, please enter 9-digit US routing number. For other international payments, please enter 8-11 characters SWIFT code'
                ]
        );
        $fieldset->addField(
                'account_number', 'text', [
            'name' => 'account_number',
            'label' => __('Account Number'),
            'title' => __('Account Number'),
            'required' => true,
            'sortOrder' => 50,
            'maxlength' => 20,
            'class' => 'validate-number'
                ]
        );
        $fieldset->addField(
                'account_type', 'select', [
            'name' => 'account_type',
            'label' => __('Account Type'),
            'title' => __('Account Type'),
            'required' => true,
            'options' => ['savings' => 'Savings', 'checking' => 'Checking'],
            'sortOrder' => 60,
                ]
        );
        $fieldset->addField(
                'holder_type', 'select', [
            'name' => 'holder_type',
            'label' => __('Holder Type'),
            'title' => __('Holder Type'),
            'required' => true,
            'options' => ['personal' => 'Personal', 'business' => 'Business'],
            'sortOrder' => 70,
                ]
        );
//        $fieldset->addField(
//                'company_name', 'text', [
//            'name' => 'company_name',
//            'label' => __('Company Name'),
//            'title' => __('Company Name'),
//            'sortOrder' => 80,
//            'maxlength' => 200,
//                ]
//        );
        $fieldset->addField(
                'bank_address', 'text', [
            'name' => 'bank_address',
            'label' => __('Bank Address'),
            'title' => __('Bank Address'),
            'sortOrder' => 90,
            'maxlength' => 200,
                ]
        );
        $fieldset->addField(
                'swift_code', 'text', [
            'name' => 'swift_code',
            'label' => __('SWIFT Code'),
            'title' => __('SWIFT Code'),
            'required' => true,
            'sortOrder' => 100,
            'maxlength' => 30,
            'class' => 'validate-alphanum',
			'note' => 'If you are receiving payouts to an International Bank Account please ensure your provide the SWIFT code. SWIFT code is not required for Domestic Bank Accounts.'
                ]
        );
        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

}
