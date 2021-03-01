<?php
/**
 * Project: Vendors.
 * User: jing
 * Date: 2019-02-26
 * Time: 15:11
 */
namespace Omnyfy\Vendor\Plugin\Sales\Model\ResourceModel;

use Magento\Sales\Model\Order;

class OrderHandlerState
{
    protected $_helper;

    public function __construct(
        \Omnyfy\Vendor\Helper\Backend $helper
    )
    {
        $this->_helper = $helper;
    }

    public function aroundCheck(
        $subject,
        callable $proceed,
        Order $order
    )
    {
        if (!$order->isCanceled() && !$order->canUnhold() && !$order->canInvoice() && !$order->canShip()) {
            if ((0 == $order->getBaseGrandTotal() || $order->canCreditmemo())
                && $this->_helper->isOrderAllShipped($order)
            ) {
                if ($order->getState() !== Order::STATE_COMPLETE) {
                    $order->setState(Order::STATE_COMPLETE)
                        ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_COMPLETE));
                }
            } elseif ((floatval($order->getTotalRefunded())
                || !$order->getTotalRefunded() && $order->hasForcedCanCreditmemo() ) && $this->_helper->isOrderAllCredited($order)
            ) {
                if ($order->getState() !== Order::STATE_CLOSED) {
                    $order->setState(Order::STATE_CLOSED)
                        ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_CLOSED));
                }
            }
        }
        if ($order->getState() == Order::STATE_NEW && $order->getIsInProcess()) {
            $order->setState(Order::STATE_PROCESSING)
                ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING));
        }
        return $subject;
    }
}