<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 13/6/17
 * Time: 5:01 PM
 */

namespace Omnyfy\Vendor\Plugin\Quote\Model\Quote\Item;

class ToOrderItem
{
    public function __construct()
    {
    }

    public function aroundConvert(
        \Magento\Quote\Model\Quote\Item\ToOrderItem $subject,
        \Closure $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        $additional = []
    )
    {
        $orderItem = $proceed($item, $additional);
        if ($item->hasData('location_id')) {
            $orderItem->setData('location_id', $item->getData('location_id'));
        }
        if ($item->hasData('vendor_id')) {
            $orderItem->setData('vendor_id', $item->getData('vendor_id'));
        }
        return $orderItem;
    }
}