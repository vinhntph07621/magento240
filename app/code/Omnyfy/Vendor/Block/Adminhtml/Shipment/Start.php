<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 27/6/17
 * Time: 5:04 PM
 */

namespace Omnyfy\Vendor\Block\Adminhtml\Shipment;

class Start extends \Magento\Backend\Block\Widget\Grid\Container
{

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_mode = 'create';
        $this->_controller = 'shipment';
        $this->_blockGroup = 'Omnyfy_Vendor';
        parent::_construct();

        $this->buttonList->remove('save');
        $this->buttonList->remove('delete');
    }

}