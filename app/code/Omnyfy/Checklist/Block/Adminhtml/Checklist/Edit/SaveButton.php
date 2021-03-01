<?php


namespace Omnyfy\Checklist\Block\Adminhtml\Checklist\Edit;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

class SaveButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label' => __('Save Checklist'),
            'class' => 'save primary',
            'data_attribute' => [
                'form-role' => 'save',
            ],
            'sort_order' => 90,
        ];
    }
}
