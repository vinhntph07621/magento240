<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-07
 * Time: 15:18
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Vendor\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Omnyfy\Core\Block\Adminhtml\Button;

class Save extends Button implements ButtonProviderInterface
{
    protected $componentContext;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\View\Element\UiComponent\Context $componentContext,
        \Magento\Framework\Registry $registry
    ) {
        $this->componentContext = $componentContext;
        parent::__construct($context, $registry);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save'),
            'class' => 'save primary',
            'data_attribute' => [
                'mage-init' => ['button' => ['event' => 'save']],
                'form-role' => 'save',
            ],
            'sort_order' => 30,
        ];
    }
}
 