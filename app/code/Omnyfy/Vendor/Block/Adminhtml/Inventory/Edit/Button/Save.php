<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 30/1/18
 * Time: 10:00 AM
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Inventory\Edit\Button;

class Save extends Generic
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'sort_order' => 10
        ];
    }
}