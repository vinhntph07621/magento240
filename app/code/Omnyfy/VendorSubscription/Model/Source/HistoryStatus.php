<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-09
 * Time: 13:14
 */
namespace Omnyfy\VendorSubscription\Model\Source;

class HistoryStatus extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const STATUS_SUCCESS = 1;
    const STATUS_FAILED = 0;

    public function toValuesArray()
    {
        return [
            self::STATUS_FAILED => __('Failed'),
            self::STATUS_SUCCESS => __('Successful'),
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
 