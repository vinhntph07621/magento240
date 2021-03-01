<?php 

namespace Omnyfy\Cms\Model\Config\Source;

class IncomeLevel implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve status options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('Low-income')],
            ['value' => 2, 'label' => __('Lower middle-income')],
            ['value' => 3, 'label' => __('Upper middle-income')],
            ['value' => 4, 'label' => __('High-income')]
        ];
    }
}