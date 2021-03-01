<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 8/10/2020
 * Time: 1:37 PM
 */

namespace Omnyfy\Vendor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;

class Order extends AbstractHelper
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * Order constructor.
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepo
     * @param Context $context
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepo,
        Context $context
    )
    {
        $this->_orderRepository = $orderRepo;
        parent::__construct($context);
    }

    public function getFuseJson($orderId){
        /** @var \Magento\Sales\Api\Data\OrderInterface $order */
        $order = $this->_orderRepository->get($orderId);
        $orderData = $order->getData();

        $orderJson = [];

        $orderJson['orderData'] = $orderData;

        foreach ($order->getAllItems() as $item) {
            $orderJson['orderItems']['items'][] = $item->getData();
        }

        $orderJson['orderType'] = 'new';
        $orderJson['billingAddress'] = $order->getBillingAddress()->getData();

        if(!is_null($order->getShippingAddress())){
            $orderJson['shipping'] = $order->getShippingAddress()->getData();
        }

        return json_encode($orderJson);
    }
}