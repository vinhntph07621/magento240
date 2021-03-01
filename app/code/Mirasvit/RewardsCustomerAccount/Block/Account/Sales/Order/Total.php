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



namespace Mirasvit\RewardsCustomerAccount\Block\Account\Sales\Order;

/**
 * Class Total
 * @package Mirasvit\RewardsCustomerAccount\Block\Account\Sales\Order
 * @deprecated
 */
class Total extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Mirasvit\Rewards\Helper\Data
     */
    protected $rewardsData;

    /**
     * @var \Magento\Framework\View\Element\Template\Context
     */
    protected $context;

    /**
     * @param \Mirasvit\Rewards\Helper\Data                    $rewardsData
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Mirasvit\Rewards\Helper\Data $rewardsData,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->rewardsData = $rewardsData;
        $this->context = $context;
        parent::__construct($context, $data);
    }

    /**
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getParentBlock()->getOrder();
    }

    /**
     * @return mixed
     */
    public function getSource()
    {
        return $this->getParentBlock()->getSource();
    }

    /**
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        if ((float) $this->getOrder()->getRewardsAmount()) {
            $source = $this->getSource();
            $value = -$source->getRewardsAmount();

            /** @var mixed $parent */
            $parent = $this->getParentBlock();
            $parent->addTotal(new \Magento\Framework\DataObject([
                'code' => 'reward_points',
                'strong' => false,
                'label' => $this->rewardsData->formatPoints($source->getRewardsPointsNumber()),
                'value' => $source instanceof \Magento\Sales\Model\Order\Creditmemo ? -$value : $value,
            ]));
        }

        return $this;
    }
}
