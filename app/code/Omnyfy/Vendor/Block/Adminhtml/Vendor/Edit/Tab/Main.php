<?php
/**
 * Copyright © 2017 Omnyfy. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Omnyfy\Vendor\Block\Adminhtml\Vendor\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;



class Main extends Generic implements TabInterface
{
    protected $profileFactory;

    protected $_wysiwygConfig;

    protected $_vendorTypeCollectionFactory;

    public function __construct(
        \Omnyfy\Vendor\Model\ProfileFactory $profileFactory,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Omnyfy\Vendor\Model\Resource\VendorType\CollectionFactory $collectionFactory,
        array $data = [])
    {
        $this->profileFactory = $profileFactory;
        $this->_wysiwygConfig = $wysiwygConfig;
        $this->_vendorTypeCollectionFactory = $collectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Profile Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Profile Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
	
	protected function _prepareLayout()
	{
		$model = $this->_coreRegistry->registry('current_omnyfy_vendor_vendor');
		$this->pageConfig->getTitle()->set(__('Add Vendor'));
		if ($model->getId()) {
			$this->pageConfig->getTitle()->set(__('Edit Vendor'));
		}	
		return parent::_prepareLayout();
	}
	
    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_omnyfy_vendor_vendor');
        //TODO: load all website ids
        $websites = $this->_storeManager->getWebsites();
        $websiteIds = [];
        foreach($websites as $id => $website) {
            $websiteIds[] = ['value' => $id, 'label' => $website->getName()];
        }

        $vendorInfo = $this->_backendSession->getVendorInfo();

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('vendor_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Profile Information')]);
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
            $fieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
            $model->setData('id', $model->getId());
            //TODO: set profiles website ids in to model
            $profileCollection = $this->profileFactory->create()->getCollection();
            $profileCollection->addFieldToFilter('vendor_id', $model->getId());
            $websiteIdsInProfile = [];
            foreach($profileCollection as $profile) {
                $websiteIdsInProfile[] = $profile->getWebsiteId();
            }
            $websiteIdsInProfile = array_unique($websiteIdsInProfile);
            if (!empty($websiteIdsInProfile)) {
                $model->setData('website_ids', $websiteIdsInProfile);
            }
        }



        $fieldset->addField(
            'name',
            'text',
            ['name' => 'name', 'label' => __('Business Name'), 'title' => __('Business Name'), 'required' => true]
        );

        if (empty($vendorInfo)) {
            $fieldset->addField(
                'type_id',
                'select',
                [
                    'name' => 'type_id',
                    'label' => __('Vendor Type'),
                    'title' => __('Vendor Type'),
                    'required' => true,
                    'options' => $this->getVendorTypeOptions()
                ]
            );
        }
        else {
            $fieldset->addField('type_id', 'hidden', ['name' => 'type_id']);
        }
/*
        $fieldset->addField(
            'address',
            'textarea',
            ['name' => 'address', 'label' => __('Business Address'), 'title' => __('Business Address'), 'required' => true]
        );
        $fieldset->addField(
            'phone',
            'text',
            ['name' => 'phone', 'label' => __('Business Contact Number'), 'title' => __('Business Contact Number'), 'required' => true, 'class' => 'validate-digits']
        );
*/
        $fieldset->addField(
            'email',
            'text',
            ['name' => 'email', 'label' => __('Business Email Address'), 'title' => __('Business Email Address'), 'required' => true, 'class' => 'validate-email', 'after_element_html' => '<small>Enter a valid email address (Ex: johndoe@domain.com) </small>']
        );

        $fieldset->addField(
            'website_ids',
            'multiselect',
            [
                'name' => 'website_ids[]',
                'label' => __('Marketplaces'),
                'title' => __('Marketplaces'),
                'required' => true,
                'values' => $websiteIds
            ]
        );

        $fieldset->addField(
            'description',
            'editor',
            [
                'name' => 'description',
                'label' => __('Business Description'),
                'title' => __('Business Description'),
                'required' => false,
                'config' => $this->_wysiwygConfig->getConfig()
            ]
        );
/*
        $fieldset->addField(
            'abn',
            'text',
            [
                'name' => 'abn',
                'label' => __('ABN'),
                'title' => __('ABN'),
                'required' => false,
                'after_element_html' => '<small>Add an Valid ABN i.e ( 53004085616 )</small>'
            ]
        );
*/
        $fieldset->addField(
                'status',
                'select',
                [
                        'name' => 'status',
                        'label' => __('Status'),
                        'title' => __('Status'),
                        'required' => true,
                        'options' => ['' => '--Select Status--', '1' => __('Active'), '0' => __('Inactive')]
                ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    protected function getVendorTypeOptions($activeOnly = false)
    {
        $collection = $this->_vendorTypeCollectionFactory->create();
        if ($activeOnly) {
            $collection->addFieldToFilter('status', \Omnyfy\Vendor\Api\Data\VendorInterface::STATUS_ENABLED);
        }
        $result = [];
        foreach($collection as $type) {
            $result[$type->getTypeId()] = $type->getTypeName();
        }
        return $result;
    }
}
