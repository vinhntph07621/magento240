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


namespace Mirasvit\RewardsCheckout\Model\KlarnaCheckout\Orderline\Items;

use /** @noinspection PhpUndefinedNamespaceInspection */
    Klarna\KcoCore\Model\Api\Parameter;
use /** @noinspection PhpUndefinedNamespaceInspection */
    Klarna\KcoCore\Model\Checkout\Orderline\DataHolder;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Sales\Api\Data\OrderInterface;

if (interface_exists('\Klarna\KcoCore\Api\OrderLineInterface', false)) {
    /** @noinspection PhpUndefinedNamespaceInspection */

    abstract class AbstractLineMediator implements \Klarna\KcoCore\Api\OrderLineInterface{

        protected $helper;

        public function __construct(/** @noinspection PhpUndefinedNamespaceInspection */
            \Klarna\KcoCore\Helper\DataConverter $helper)
        {
            $this->helper = $helper;
        }
    }
} else {
    abstract class AbstractLineMediator {}
}

class RewardsDiscount extends AbstractLineMediator
{
    const ITEM_TYPE_REWARDS = 'rewards_calculations';

    /**
     * Order line code name
     *
     * @var string
     */
    private $code;

    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function collectPrePurchase(Parameter $parameter, DataHolder $dataHolder, CartInterface $quote)
    {
        return $this->collect($parameter, $dataHolder);
    }

    /**
     * {@inheritdoc}
     */
    public function collectPostPurchase(Parameter $parameter, DataHolder $dataHolder, OrderInterface $order)
    {
        return $this->collect($parameter, $dataHolder);
    }

    /**
     * {@inheritdoc}
     */
    public function collect(Parameter $checkout, DataHolder $dataHolder)
    {
        $totals = $dataHolder->getTotals();

        if (is_array($totals) && isset($totals['rewards_calculations'])) {
            $total = $totals['rewards_calculations'];
            $value = $this->helper->toApiFloat($total->getValue());

            $checkout->setRewardsUnitPrice($value)
                ->setRewardsTaxRate(0)
                ->setRewardsTotalAmount($value)
                ->setRewardsTaxAmount(0)
                ->setRewardsTitle((string)$total->getTitle())
                ->setRewardsReference($total->getCode());
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(Parameter $checkout)
    {
        if ($checkout->getRewardsTotalAmount()) {
            $title = __('Rewards Discount')->getText();
            $checkout->addOrderLine([
                'type'             => 'discount',
                'reference'        => $checkout->getRewardsReference(),
                'name'             => $title,
                'quantity'         => 1,
                'unit_price'       => $checkout->getRewardsUnitPrice(),
                'tax_rate'         => $checkout->getRewardsTaxRate(),
                'total_amount'     => $checkout->getRewardsTotalAmount(),
                'total_tax_amount' => $checkout->getRewardsTaxAmount(),
            ]);
        }

        return $this;
    }
}