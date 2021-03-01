<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 7/6/17
 * Time: 3:34 PM
 */
namespace Omnyfy\Vendor\Block\Adminhtml;

use Magento\Backend\Block\Widget\Grid\Container;

class Vendor extends Container
{
    protected function _construct()
    {
        $this->_controller = 'vendor';
        $this->_headerText = __('Vendor');
        //$this->_addButtonLabel = __('Add new Vendor');

        parent::_construct();
        $vendorInfo = $this->_backendSession->getVendorInfo();
        if (!empty($vendorInfo)) {
            $this->removeButton('add');
        }
        $this->removeButton('add');
    }
}