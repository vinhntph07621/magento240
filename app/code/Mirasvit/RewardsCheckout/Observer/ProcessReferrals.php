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

use Magento\Sales\Model\Order;
use Mirasvit\Rewards\Helper\Balance\EarnBehaviorOrderPoints;

class ProcessReferrals implements \Magento\Framework\Event\ObserverInterface
{
    private $earnBehaviorOrderPoints;

    public function __construct(
        EarnBehaviorOrderPoints $earnBehaviorOrderPoints
    ) {
        $this->earnBehaviorOrderPoints = $earnBehaviorOrderPoints;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        \Magento\Framework\Profiler::start(__CLASS__.':'.__METHOD__);

        /** @var Order $order */
        $order = $observer->getEvent()->getOrder();
        if ($order && $order->getId()) {
            $this->earnBehaviorOrderPoints->earnBehaviorOrderPoints($order);
        }

        \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
    }
}
