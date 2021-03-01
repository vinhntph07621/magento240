<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 17/7/17
 * Time: 2:38 PM
 */
namespace Omnyfy\Vendor\Block\Adminhtml\Shipment\Location;

use Magento\Framework\DataObject;

class ToShip extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
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

        $content = '<a href="'
            . $this->getUrl('adminhtml/order_shipment/new', ['order_id' => $orderId ,'location_id' => $locationId]) . '"'
            . ' onclick="to_ship(' . $locationId. ')" >' . __('Ship') . '</a>';

        return $content;
    }
}