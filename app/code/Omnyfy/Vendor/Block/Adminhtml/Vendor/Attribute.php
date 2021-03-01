<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-08
 * Time: 13:38
 */

namespace Omnyfy\Vendor\Block\Adminhtml\Vendor;

class Attribute extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_vendor_attribute';
        $this->_blockGroup = 'Omnyfy_Vendor';
        $this->_headerText = __('Vendor Attributes');
        $this->_addButtonLabel = __('Add New Attribute');
        parent::_construct();
    }
}