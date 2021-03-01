<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 17/7/17
 * Time: 11:57 AM
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Shipment\Location;

use Magento\Framework\DataObject;

class OrderItems extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    protected $orderRepository;

    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Backend\Block\Context $context,
        array $data = [])
    {
        $this->orderRepository = $orderRepository;
        parent::__construct($context, $data);
    }

    public function render(DataObject $row)
    {
        $locationId = $row->getEntityId();
        $orderId = $this->getRequest()->getParam('order_id');

        $order = $this->orderRepository->get($orderId);
        $content = '';
        $items = [];
        foreach($order->getItems() as $item) {
            if ($item->getQtyToShip() <= 0 ) {
                continue;
            }
            if ($locationId != $item->getLocationId()) {
                continue;
            }

            $items[$item->getId()] = $item->getQtyToShip();
            $content .= '<span>' . $item->getName() .' x ' . $item->getQtyToShip() . '</span>';
        }

        if (!empty($items)) {
            $input = '<input type="hidden" name="items" value="' . json_encode(($items)) . '" />';
            $content = '<div>' . $content . $input . '</div>';
        }

        return $content;
    }
}