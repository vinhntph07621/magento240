<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-03
 * Time: 17:47
 */
namespace Omnyfy\Vendor\Model\Source;

class ViewMode extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 0,
                'label' => __('Grid View')
            ],
            [
                'value' => 1,
                'label' => __('List View')
            ]
        ];
    }

    public function toValuesArray()
    {
        return [
            0 => __('Grid View'),
            1 => __('List View')
        ];
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
 