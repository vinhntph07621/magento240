<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rewards
 * @version   3.0.21
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\RewardsCheckout\Observer;

class RestorePointsOnOrderCancelAfter implements \Magento\Framework\Event\ObserverInterface
{
    private $cancelEarnedPoints;
    private $orderService;
    private $restoreSpentPoints;

    public function __construct(
        \Mirasvit\Rewards\Service\Order $orderService,
        \Mirasvit\Rewards\Service\Order\Transaction\CancelEarnedPoints $cancelEarnedPoints,
        \Mirasvit\Rewards\Service\Order\Transaction\RestoreSpentPoints $restoreSpentPoints
    ) {
        $this->cancelEarnedPoints = $cancelEarnedPoints;
        $this->orderService       = $orderService;
        $this->restoreSpentPoints = $restoreSpentPoints;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        \Magento\Framework\Profiler::start(__CLASS__.':'.__METHOD__);
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        if (!$this->orderService->isLocked($order)) {
            $this->orderService->lock($order);
            if ($order->getCustomerId()) {
                $this->restoreSpentPoints->createTransaction($order);
                $this->cancelEarnedPoints->createTransaction($order, false);
            }
        }
        \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
    }
}
