<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-03
 * Time: 17:48
 */
namespace Omnyfy\Vendor\Model\Source;

class Status extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public function toOptionArray()
    {
        return [
            [
                'value' => self::STATUS_INACTIVE,
                'label' => __('Inactive')
            ],
            [
                'value' => self::STATUS_ACTIVE,
                'label' => __('Active')
            ]
        ];
    }

    public function toValuesArray()
    {
        return [
            self::STATUS_INACTIVE => __('Inactive'),
            self::STATUS_ACTIVE => __('Active')
        ];
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
 