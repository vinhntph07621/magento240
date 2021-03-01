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


namespace Mirasvit\RewardsCheckout\Model\KlarnaPayment\Orderline\Items;

use Klarna\Core\Api\BuilderInterface;

if (class_exists('\Klarna\Core\Model\Checkout\Orderline\AbstractLine')) {
    abstract class AbstractLineMediator extends \Klarna\Core\Model\Checkout\Orderline\AbstractLine {}
} else {
    abstract class AbstractLineMediator {}
}

class RewardsDiscount extends AbstractLineMediator
{
    const ITEM_TYPE_REWARDS = 'discount';

    /**
     * {@inheritdoc}
     */
    public function collect(BuilderInterface $checkout)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $checkout->getObject();
        $totals = $quote->getTotals();

        if (is_array($totals) && isset($totals['rewards_calculations'])) {
            $total = $totals['rewards_calculations'];
            $value = $this->helper->toApiFloat($total->getValue());

            $checkout->addData([
                'rewards_unit_price'   => $value,
                'rewards_tax_rate'     => 0,
                'rewards_total_amount' => $value,
                'rewards_tax_amount'   => 0,
                'rewards_title'        => (string)$total->getTitle(),
                'rewards_reference'    => $total->getCode()

            ]);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(BuilderInterface $checkout)
    {
        if ($checkout->getRewardsTotalAmount()) {
            $title = __('Rewards Discount')->getText();
            $checkout->addOrderLine([
                'type'             => self::ITEM_TYPE_REWARDS,
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