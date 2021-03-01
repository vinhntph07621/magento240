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



namespace Mirasvit\RewardsBehavior\Observer;

use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Rewards\Model\Config;

class EarnOnMirasvitAdvnCustomerSubscribed implements ObserverInterface
{
    /**
     * @var \Mirasvit\Rewards\Helper\BehaviorRule
     */
    protected $rewardsBehavior;

    /**
     * @param \Mirasvit\Rewards\Helper\BehaviorRule $rewardsBehavior
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\BehaviorRule $rewardsBehavior
    ) {
        $this->rewardsBehavior = $rewardsBehavior;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        \Magento\Framework\Profiler::start(__CLASS__.':'.__METHOD__);
        $this->rewardsBehavior->processRule(Config::BEHAVIOR_TRIGGER_NEWSLETTER_SIGNUP);
        \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);
    }
}
