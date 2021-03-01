<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 11/9/19
 * Time: 10:43 am
 */
namespace Omnyfy\VendorSubscription\Block\Adminhtml\Subscription\Update\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Omnyfy\Core\Block\Adminhtml\Button;

class Save extends Button implements ButtonProviderInterface
{
    public function getButtonData()
    {
        return [
            'label' => __('Confirm Plan Change'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 30,
        ];
    }
}