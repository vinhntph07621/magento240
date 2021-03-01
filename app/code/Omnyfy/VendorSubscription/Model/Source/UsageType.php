<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-17
 * Time: 10:11
 */
namespace Omnyfy\VendorSubscription\Model\Source;

class UsageType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const PRODUCT = 1;
    const KIT_STORE = 2;
    const ENQUIRY = 3;
    const REQUEST_FOR_QUOTE = 4;

    public function toValuesArray()
    {
        return [
            self::PRODUCT => __('Product'),
            self::KIT_STORE => __('KIT_STORE'),
            self::ENQUIRY => __('Enquiry'),
            self::REQUEST_FOR_QUOTE => __('Request For Quote'),
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
 