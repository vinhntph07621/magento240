<?php
/**
 * Copyright © 2017 Omnyfy. All rights reserved.
 */

// @codingStandardsIgnoreFile

namespace Omnyfy\Vendor\Block\Adminhtml\Vendor\Edit\Tab;


use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;



class Media extends Generic implements TabInterface
{

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Media Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Media Information');
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
		//$this->pageConfig->getTitle()->set(__('Add Media'));
		if ($model->getId()) {
			//$this->pageConfig->getTitle()->set(__('Edit Media'));
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
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('vendor_');
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Media Information')]);
        if ($model->getId()) {
            $fieldset->addField('vendor_id', 'hidden', ['name' => 'vendor_id']);
        }
		$fieldset->addField(
			 'logo',
			 'image',
			 [
				'name' => 'logo',
				'label' => __('Logo'),
				'title' => __('Logo'),
				'class' => 'vendor_logo',
				'required' => false,
				'note' => 'Allow image type: jpg, jpeg, gif, png',
			  ]
		);	
		$fieldset->addField(
			 'banner',
			 'image',
			 [
				'name' => 'banner',
				'label' => __('Banner'),
				'title' => __('Banner'),
				'class' => 'vendor_banner',
				'required' => false,
				'note' => 'Allow image type: jpg, jpeg, gif, png',
			  ]
		);
	    $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
