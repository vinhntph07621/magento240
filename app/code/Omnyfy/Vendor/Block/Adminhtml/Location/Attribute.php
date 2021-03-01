<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-01
 * Time: 16:01
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Location;

class Attribute extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_location_attribute';
        $this->_blockGroup = 'Omnyfy_Vendor';
        $this->_headerText = __('Location Attributes');
        $this->_addButtonLabel = __('Add New Attribute');
        parent::_construct();
    }
}
 