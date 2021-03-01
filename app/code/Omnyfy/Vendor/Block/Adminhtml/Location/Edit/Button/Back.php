<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-07-23
 * Time: 16:26
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Location\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Omnyfy\Core\Block\Adminhtml\Button;

class Back extends Button implements ButtonProviderInterface
{
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
 