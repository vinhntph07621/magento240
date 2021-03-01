<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/8/17
 * Time: 11:27 AM
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Location\Edit;

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
        $this->setId('omnyfy_vendor_location_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Location'));
    }

    protected function _prepareLayout()
    {
        $this->addTab('main_section',
            [
                'label' => __('Information'),
                'title' => __('Information'),
                'block' => 'omnyfy_vendor_location_edit_tab_main'
            ]
        );
        return parent::_prepareLayout();
    }


}