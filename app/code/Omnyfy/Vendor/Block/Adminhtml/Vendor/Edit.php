<?php
/**
 * Copyright © 2017 Omnyfy. All rights reserved.
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Vendor;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_vendor';
        $this->_blockGroup = 'Omnyfy_Vendor';

        parent::_construct();

        $this->buttonList->add(
            'save_and_continue_edit',
            [
                'class' => 'save',
                'label' => __('Save and Continue Edit'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form']],
                ]
            ],
            10
        );

        $this->_eventManager->dispatch('omnyfy_vendor_edit_form_add_button', ['button_list' => $this->buttonList]);

        $this->removeButton('delete');
    }

    /**
     * Getter for form header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        $vendor = $this->_coreRegistry->registry('current_omnyfy_vendor_vendor');
        if ($vendor->getId()) {
            return __("Edit Vendor '%1'", $this->escapeHtml($vendor->getName()));
        } else {
            return __('New Vendor');
        }
    }

    protected function _prepareLayout()
    {
        $vendor = $this->_coreRegistry->registry('current_omnyfy_vendor_vendor');
        if (!empty($vendor) && $vendor->getId()) {
            $title = __("Edit Vendor '%1'", $this->escapeHtml($vendor->getName()));

        } else {
            $title = __('New Vendor');
        }

        // check if the block exists before trying to set page title
        if ($this->getLayout()->getBlock('page.title')) {
            $this->getLayout()->getBlock('page.title')->setPageTitle($title);
        }

        return parent::_prepareLayout();
    }
}
