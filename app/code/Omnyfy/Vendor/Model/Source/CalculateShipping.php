<?php
namespace Omnyfy\Vendor\Model\Source;

class CalculateShipping extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const PER_VENDOR = 'per_vendor';
    const OVERALL_CART = 'overall_cart';

    public function toOptionArray()
    {
        return [
            [
                'value' => self::PER_VENDOR,
                'label' => __('Per Vendor')
            ],
            [
                'value' => self::OVERALL_CART,
                'label' => __('Overall Cart')
            ]
        ];
    }

    public function toValuesArray()
    {
        return [
            self::PER_VENDOR => __('Per Vendor'),
            self::OVERALL_CART => __('Overall Cart')
        ];
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
