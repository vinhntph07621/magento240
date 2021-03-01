<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 2/06/2020
 * Time: 12:29 PM
 */

namespace Omnyfy\VendorSearch\Model\Config\Source;


class LocationType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 1,
                'label' => __('Location')
            ],
            [
                'value' => 2,
                'label' => __('Booking')
            ]
        ];
    }

    public function toValuesArray()
    {
        return [
            1 => __('Location'),
            2 => __('Booking')
        ];
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}