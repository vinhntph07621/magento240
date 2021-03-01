<?php
/**
 * Copyright © 2017 Omnyfy. All rights reserved.
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Vendor\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('omnyfy_vendor_vendor_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Vendor'));
    }

    protected function _prepareLayout()
    {

        $this->addTab('main_section',
            [
                'label' => __('Profile'),
                'title' => __('Profile'),
                'block' => 'omnyfy_vendor_vendor_edit_tab_main'
            ]
        );

        return parent::_prepareLayout();
    }

    protected function _beforeToHtml()
    {
        $this->_eventManager->dispatch('omnyfy_vendor_edit_tabs_before_html', ['tabs' => $this]);

        return parent::_beforeToHtml();
    }
}
