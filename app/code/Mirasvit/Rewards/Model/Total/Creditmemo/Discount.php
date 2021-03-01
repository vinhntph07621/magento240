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



namespace Mirasvit\Rewards\Model\Total\Creditmemo;

use Magento\Sales\Model\Order\Creditmemo\Total\AbstractTotal;
use Mirasvit\Rewards\Api\Service\RefundServiceInterface;
use Mirasvit\Rewards\Helper\Calculation;
use Mirasvit\Rewards\Helper\Purchase;
use Mirasvit\Rewards\Service\Order\Transaction\CancelEarnedPoints;

class Discount extends AbstractTotal
{
    /**
     * @var RefundServiceInterface
     */
    private $refundService;
    /**
     * @var Purchase
     */
    private $rewardsPurchase;

    private $cancelEarnedPointsService;

    public function __construct(
        RefundServiceInterface $refundService,
        Purchase $rewardsPurchase,
        CancelEarnedPoints $cancelEarnedPointsService,
        array $data = []
    ) {
        parent::__construct($data);

        $this->refundService             = $refundService;
        $this->rewardsPurchase           = $rewardsPurchase;
        $this->cancelEarnedPointsService = $cancelEarnedPointsService;
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     *
     * @return $this
     */
    public function collect(\Magento\Sales\Model\Order\Creditmemo $creditmemo)
    {
        parent::collect($creditmemo);

        $order = $creditmemo->getOrder();
        $purchase = $this->rewardsPurchase->getByOrder($order);

        if (!$purchase) {
            return $this;
        }

        $proportion = $this->getProportion($creditmemo);
        $spendAmount = round($purchase->getSpendAmount() * $proportion, 2);
        $baseSpendAmount = round($purchase->getBaseSpendAmount() * $proportion, 2);

        $creditmemo->setRewardsDiscountAmount($spendAmount);
        $creditmemo->setBaseRewardsDiscountAmount($baseSpendAmount);

        $spentPoints = round($purchase->getSpendPoints() * $proportion, 0);
        $creditmemo->setRewardsSpendPoints($spentPoints);
        $earnedPoints = round($purchase->getEarnPoints() * $proportion, 0);
        $creditmemo->setRewardsEarnPoints($earnedPoints);

        $items = $order->getItemsCollection();
        $itemsLeft = 0;
        $itemsRefunding = 0;
        $lastCreditmemo = false;
        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($items as $item) {
            if ($item->canRefund()) {
                $itemsLeft += $item->getQtyToRefund();
            }
        }
        foreach ($creditmemo->getItems() as $item) {
            if ($item->getOrderItem()->getParentItem()) {
                continue;
            }
            $itemsRefunding += $item->getQty();
        }
        if ($itemsRefunding == $itemsLeft) {
            $lastCreditmemo = true;
        }
        $refundInfo = $this->refundService->getByOrderId($order->getId());
        if ($creditmemo->getBaseGrandTotal()) {
            $amount = $creditmemo->getBaseGrandTotal() - $baseSpendAmount;
            if ($amount < 0) {// due to shipping discount the amount can be less then 0
                $creditmemo->setBaseGrandTotal(0);
            } else {
                if ($lastCreditmemo) {
                    $leftToRefund = $order->getBaseTotalInvoiced() - $order->getBaseTotalOfflineRefunded() -
                        $order->getBaseTotalOnlineRefunded() - $refundInfo->getBaseRefundedSum();
                    $leftShipping = $order->getBaseShippingAmount() - $order->getBaseShippingRefunded();
                    if ($creditmemo->getBaseShippingAmount() < $leftShipping) {
                        $leftToRefund -= ($leftShipping - $creditmemo->getBaseShippingAmount());
                    }
                    if (abs($leftToRefund - $amount) > Calculation::ZERO_VALUE) {
                        $amount = $leftToRefund;
                    }
                    $amount -= $creditmemo->getBaseAdjustmentNegative();
                    $amount += $creditmemo->getBaseAdjustmentPositive();
                    if ($amount < 0) {
                        $amount = 0;
                    }
                }
                $creditmemo->setBaseGrandTotal($amount);
            }
        }
        if ($creditmemo->getGrandTotal()) {
            $amount = $creditmemo->getGrandTotal() - $spendAmount;
            if ($amount < 0) {// due to shipping discount the amount can be less then 0
                $creditmemo->setGrandTotal(0);
            } else {
                if ($lastCreditmemo) {
                    $leftToRefund = $order->getTotalInvoiced() - $order->getTotalOfflineRefunded() -
                        $order->getTotalOnlineRefunded() - $refundInfo->getRefundedSum();
                    $leftShipping = $order->getShippingAmount() - $order->getShippingRefunded();
                    if ($creditmemo->getShippingAmount() < $leftShipping) {
                        $leftToRefund -= ($leftShipping - $creditmemo->getShippingAmount());
                    }
                    if (abs($leftToRefund - $amount) > Calculation::ZERO_VALUE) {
                        $amount = $leftToRefund;
                    }
                    $amount -= $creditmemo->getAdjustmentNegative();
                    $amount += $creditmemo->getAdjustmentPositive();
                    if ($amount < 0) {
                        $amount = 0;
                    }
                }
                $creditmemo->setGrandTotal($amount);
            }
        }

        return $this;
    }

    /**
     * @param \Magento\Sales\Model\Order\Creditmemo $creditmemo
     * @return float
     */
    private function getProportion($creditmemo)
    {
        $order = $creditmemo->getOrder();
        if ($order->getSubtotal() > 0) {
            $proportion = $creditmemo->getSubtotal() / $order->getSubtotal();
        } else { // for zero orders with earning points
            $proportion = $this->cancelEarnedPointsService->getCreditmemoItemsQty($creditmemo) /
                $this->cancelEarnedPointsService->getCreditmemoOrderItemsQty($creditmemo);
        }
        if ($proportion > 1) {
            $proportion = 1;
        }

        return $proportion;
    }
}
