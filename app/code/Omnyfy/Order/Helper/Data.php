<?php


namespace Omnyfy\Order\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    protected $_orderRepository;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->_orderRepository = $orderRepository;
        parent::__construct($context);
    }

    /**
     * Check if the order has a Alcohol product
     * @param $orderId
     * @return bool
     */
    public function hasAlcoholProduct($orderId){
        if ($order = $this->_orderRepository->get($orderId)){
            foreach ($order->getItems() as $item){
                if ($item->getProduct()->getData('alcohol_product')){
                    return true;
                }
            }
        }
        return false;
    }
}
