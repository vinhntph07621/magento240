<?php
/**
 * Project: Strip API
 * User: jing
 * Date: 2019-07-17
 * Time: 13:25
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Vendor\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Omnyfy\Core\Block\Adminhtml\Button;

class Location extends Button implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Manage Location'),
            'on_click' => sprintf("location.href = '%s';", $this->getUrl('*/location/')),
            'class' => 'primary',
            'sort_order' => 20
        ];
    }
}
 