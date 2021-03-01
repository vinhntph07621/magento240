<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 1/06/2020
 * Time: 4:14 PM
 */

namespace Omnyfy\VendorSearch\Model\Config\Source;


class LocationPage implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [['value' => 1, 'label' => __('Location Page')], ['value' => 2, 'label' => __('Booking Location Page')]];
    }
}