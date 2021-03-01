<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-05
 * Time: 11:29
 */
namespace Omnyfy\VendorSubscription\Model\Source;

class Status extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;

    public function toValuesArray()
    {
        return [
            self::STATUS_INACTIVE => __('Inactive'),
            self::STATUS_ACTIVE => __('Active')
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
 