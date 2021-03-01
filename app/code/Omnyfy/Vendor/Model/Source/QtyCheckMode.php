<?php
/**
 * Project: apcd.
 * User: jing
 * Date: 5/9/18
 * Time: 8:33 PM
 */
namespace Omnyfy\Vendor\Model\Source;

class QtyCheckMode extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    public function toOptionArray()
    {
        return [
            [
                'value' => \Omnyfy\Vendor\Model\Config::QTY_CHECK_MODE_ALL,
                'label' => __('All Location')
            ],
            [
                'value' => \Omnyfy\Vendor\Model\Config::QTY_CHECK_MODE_WAREHOUSE_ONLY,
                'label' => __('Warehouse Only')
            ]
        ];
    }

    public function toValuesArray()
    {
        return [
            \Omnyfy\Vendor\Model\Config::QTY_CHECK_MODE_ALL => __('All Location'),
            \Omnyfy\Vendor\Model\Config::QTY_CHECK_MODE_WAREHOUSE_ONLY => __('Warehouse Only')
        ];
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}