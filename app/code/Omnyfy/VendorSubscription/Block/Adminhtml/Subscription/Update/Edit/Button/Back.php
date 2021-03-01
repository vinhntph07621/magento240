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

class Back extends Button implements ButtonProviderInterface
{
    public function getButtonData()
    {
        $url = $this->getUrl('omnyfy_vendor/vendor/index');
        return [
            'label' => __('Back'),
            'on_click' => sprintf("location.href = '%s';", $url),
            'class' => 'back',
            'sort_order' => 10
        ];
    }
}
 