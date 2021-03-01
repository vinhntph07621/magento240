<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 12/9/19
 * Time: 2:45 pm
 */
namespace Omnyfy\VendorSubscription\Model\Source;

class UpdateStatus extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const STATUS_PENDING = 0;
    const STATUS_DONE = 1;
    const STATUS_FAILED = 2;
    const STATUS_PROCESSING = 3;

    public function toValuesArray()
    {
        return [
            self::STATUS_PENDING => __('Pending'),
            self::STATUS_DONE => __('Complete'),
            self::STATUS_FAILED => __('Failed'),
            self::STATUS_PROCESSING => __('Processing'),
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
 