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


namespace Mirasvit\RewardsCheckout\Klarna\KcoCore\Model\Api;

if (class_exists('\Klarna\KcoCore\Model\Api\Parameter')) {
    /** @noinspection PhpUndefinedNamespaceInspection */

    abstract class AbstractParameter extends \Klarna\KcoCore\Model\Api\Parameter{}
} else {
    abstract class AbstractParameter {}
}

class Parameter extends AbstractParameter
{
    /** @var float $rewardsUnitPrice */
    private $rewardsUnitPrice;

    /** @var float $rewardsTaxRate */
    private $rewardsTaxRate;

    /** @var float $rewardsTotalAmount */
    private $rewardsTotalAmount;

    /** @var float $rewardsTaxAmount */
    private $rewardsTaxAmount;

    /** @var string $rewardsTitle */
    private $rewardsTitle;

    /** @var string $rewardsReference */
    private $rewardsReference;

    /**
     * Getting back the reward unit price
     *
     * @return float
     */
    public function getRewardsUnitPrice()
    {
        return $this->rewardsUnitPrice;
    }

    /**
     * Setting the reward unit price
     *
     * @param float $rewardUnitPrice
     * @return $this
     */
    public function setRewardsUnitPrice($rewardUnitPrice)
    {
        $this->rewardsUnitPrice = $rewardUnitPrice;
        return $this;
    }

    /**
     * Getting back the reward tax rate
     *
     * @return float
     */
    public function getRewardsTaxRate()
    {
        return $this->rewardsTaxRate;
    }

    /**
     * Setting the reward tax rate
     *
     * @param float $rewardTaxRate
     * @return $this
     */
    public function setRewardsTaxRate($rewardTaxRate)
    {
        $this->rewardsTaxRate = $rewardTaxRate;
        return $this;
    }

    /**
     * Getting back the reward total amount
     *
     * @return float
     */
    public function getRewardsTotalAmount()
    {
        return $this->rewardsTotalAmount;
    }

    /**
     * Setting the reward total amount
     *
     * @param float $rewardTotalAmount
     * @return $this
     */
    public function setRewardsTotalAmount($rewardTotalAmount)
    {
        $this->rewardsTotalAmount = $rewardTotalAmount;
        return $this;
    }

    /**
     * Getting back the reward tax amount
     *
     * @return float
     */
    public function getRewardsTaxAmount()
    {
        return $this->rewardsTaxAmount;
    }

    /**
     * Setting the reward tax amount
     *
     * @param float $rewardTaxAmount
     * @return $this
     */
    public function setRewardsTaxAmount($rewardTaxAmount)
    {
        $this->rewardsTaxAmount = $rewardTaxAmount;
        return $this;
    }

    /**
     * Getting back the reward title
     *
     * @return string
     */
    public function getRewardsTitle()
    {
        return $this->rewardsTitle;
    }

    /**
     * Setting the reward title
     *
     * @param string $rewardTitle
     * @return $this
     */
    public function setRewardsTitle($rewardTitle)
    {
        $this->rewardsTitle = $rewardTitle;
        return $this;
    }

    /**
     * Getting back the reward reference
     *
     * @return string
     */
    public function getRewardsReference()
    {
        return $this->rewardsReference;
    }

    /**
     * Setting th reward reference
     *
     * @param string $rewardReference
     * @return $this
     */
    public function setRewardsReference($rewardReference)
    {
        $this->rewardsReference = $rewardReference;
        return $this;
    }
}
