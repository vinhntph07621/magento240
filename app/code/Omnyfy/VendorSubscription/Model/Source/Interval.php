<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-05
 * Time: 11:15
 */
namespace Omnyfy\VendorSubscription\Model\Source;

class Interval extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const ONE_OFF = -1;
    const DAILY = 0;
    const MONTHLY = 1;
    const TREE_MONTHS = 2;
    const SIX_MONTHS = 3;
    const ANNUALLY = 4;

    public function toValuesArray()
    {
        return [
            self::ONE_OFF => __('One off'),
            self::DAILY => __('Daily'),
            self::MONTHLY => __('Monthly'),
            self::TREE_MONTHS => __('3 Months'),
            self::SIX_MONTHS => __('6 Months'),
            self::ANNUALLY => __('Annually'),
        ];
    }

    public function toOptionArray()
    {
        $result = [];
        foreach($this->toValuesArray() as $key => $val) {
            $result[] = [
                'value' => $key,
                'label' => $val
            ];
        }
        return $result;
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
 