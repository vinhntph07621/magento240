<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/8/17
 * Time: 9:23 AM
 */
namespace Omnyfy\Vendor\Block\Adminhtml;

class Location extends \Magento\Backend\Block\Widget\Grid\Container
{
    protected function _construct()
    {
        $this->_controller = 'location';
        $this->_headerText = __('Location');
        $this->_addButtonLabel = __('Add New Location / Warehouse');
        parent::_construct();
    }
}