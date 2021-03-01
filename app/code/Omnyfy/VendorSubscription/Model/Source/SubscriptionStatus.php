<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-09
 * Time: 10:37
 */
namespace Omnyfy\VendorSubscription\Model\Source;

class SubscriptionStatus extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_CANCELLED = 2;
    const STATUS_PENDING_CANCEL = 3;
    const STATUS_DELETED = 4;
    const STATUS_PENDING_ACTIVE = -1;

    public function toValuesArray()
    {
        return [
            self::STATUS_INACTIVE => __('Inactive'),
            self::STATUS_ACTIVE => __('Active'),
            self::STATUS_CANCELLED => __('Cancelled'),
            self::STATUS_PENDING_CANCEL => __('Pending cancel'),
            self::STATUS_DELETED => __('Deleted'),
            self::STATUS_PENDING_ACTIVE => __('Incomplete'),
        ];
    }

    public function getAllOptions()
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
}
 