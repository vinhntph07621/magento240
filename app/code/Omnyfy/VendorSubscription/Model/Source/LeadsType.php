<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 27/9/19
 * Time: 7:25 pm
 */
namespace Omnyfy\VendorSubscription\Model\Source;

class LeadsType extends \Omnyfy\Core\Model\Source\AbstractSource
{
    const ENQUIRY = 3;
    const REQUEST_FOR_QUOTE = 4;

    public function toValuesArray()
    {
        return [
            self::ENQUIRY => __('Enquiry'),
            self::REQUEST_FOR_QUOTE => __('Request For Quote'),
        ];
    }
}