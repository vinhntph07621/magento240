<?php
/**
 * Project: Core Module.
 * User: jing
 * Date: 24/9/17
 * Time: 4:19 AM
 */
namespace Omnyfy\Core\Model\Source;

class BooleanActive implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 0, 'label' => __('Inactive')],
            ['value' => 1, 'label' => __('Active')],
        ];
    }
}
