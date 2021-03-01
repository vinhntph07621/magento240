<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-10
 * Time: 16:24
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Vendor\Type\Edit\Button;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Omnyfy\Core\Block\Adminhtml\Button;

class Save extends Button implements ButtonProviderInterface
{
    protected $componentContext;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\View\Element\UiComponent\Context $componentContext,
        \Magento\Framework\Registry $registry
    )
    {
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

//        $id = $this->componentContext->getRequestParam('id');
//        $url = empty($id) ? $this->getUrl('*/*/save') : $this->getUrl('*/*/save', ['id' => $id]);
//        return [
//            'label' => __('Save'),
//            'class' => 'save primary',
//            'data_attribute' => [
//                'mage-init' => ['button' => ['event' => 'save']],
//                'form-role' => 'save',
//            ],
//            'url' => $url,
//            'sort_order' => 15
//        ];

    }
}
 