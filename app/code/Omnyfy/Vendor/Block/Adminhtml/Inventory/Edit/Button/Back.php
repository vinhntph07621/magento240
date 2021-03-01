<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 30/1/18
 * Time: 9:59 AM
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Inventory\Edit\Button;

class Back extends Generic
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/*/')),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
}