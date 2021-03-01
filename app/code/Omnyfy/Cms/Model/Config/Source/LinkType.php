<?php 

namespace Omnyfy\Cms\Model\Config\Source;

class LinkType implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve status options array.
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 1, 'label' => __('URL')],
            ['value' => 0, 'label' => __('Document')]
        ];
    }
}