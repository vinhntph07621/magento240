<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 9/11/18
 * Time: 10:09 PM
 */
namespace Omnyfy\Vendor\Observer;

class RefundQty implements \Magento\Framework\Event\ObserverInterface
{
    protected $_vendorResource;

    public function __construct(
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource
    )
    {
        $this->_vendorResource = $vendorResource;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $creditMemo = $observer->getCreditmemo();

        $items = $creditMemo->getAllItems();

        $data = [];

        foreach($items as $item) {
            $orderItem = $item->getOrderItem();

            $data[] = [
                'product_id' => $orderItem->getProductId(),
                'location_id' => $orderItem->getLocationId(),
                'qty' => $item->getQty()
            ];
        }

        if (empty($data)) {
            $items = $creditMemo->getInvoice()->getAllItems();

            foreach($items as $item) {
                $data[] = [
                    'product_id' => $item->getProductId(),
                    'location_id' => $item->getLocationId(),
                    'qty' => $item->getQty()
                ];
            }
        }

        $this->_vendorResource->returnQty($data);
    }
}