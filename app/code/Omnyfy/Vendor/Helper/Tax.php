<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 9/8/17
 * Time: 8:58 PM
 */
namespace Omnyfy\Vendor\Helper;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Store\Model\Store;
use Magento\Customer\Model\Address;
use Magento\Tax\Model\Config;
use Magento\Tax\Api\TaxCalculationInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Tax\Api\OrderTaxManagementInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Tax\Api\Data\OrderTaxDetailsItemInterface;
use Magento\Sales\Model\EntityInterface;

class Tax extends \Magento\Tax\Helper\Data
{
    protected function calculateTaxForItems(EntityInterface $order, EntityInterface $salesItem)
    {
        $taxClassAmount = [];

        $orderTaxDetails = $this->orderTaxManagement->getOrderTaxDetails($order->getId());

        // Apply any taxes for the items
        /** @var $item \Magento\Sales\Model\Order\Invoice\Item|\Magento\Sales\Model\Order\Creditmemo\Item */
        foreach ($salesItem->getItems() as $item) {
            $orderItem = $item->getOrderItem();
            if (empty($orderItem)) continue;
            $orderItemId = $orderItem->getId();
            $orderItemTax = $orderItem->getTaxAmount();
            $itemTax = $item->getTaxAmount();
            if (!$itemTax || !floatval($orderItemTax)) {
                continue;
            }
            //An invoiced item or credit memo item can have a different qty than its order item qty
            $itemRatio = $itemTax / $orderItemTax;
            $itemTaxDetails = $orderTaxDetails->getItems();
            foreach ($itemTaxDetails as $itemTaxDetail) {
                //Aggregate taxable items associated with an item
                if ($itemTaxDetail->getItemId() == $orderItemId) {
                    $taxClassAmount = $this->_aggregateTaxes($taxClassAmount, $itemTaxDetail, $itemRatio);
                } elseif ($itemTaxDetail->getAssociatedItemId() == $orderItemId) {
                    $taxableItemType = $itemTaxDetail->getType();
                    $ratio = $itemRatio;
                    if ($item->getTaxRatio()) {
                        $taxRatio = unserialize($item->getTaxRatio());
                        if (isset($taxRatio[$taxableItemType])) {
                            $ratio = $taxRatio[$taxableItemType];
                        }
                    }
                    $taxClassAmount = $this->_aggregateTaxes($taxClassAmount, $itemTaxDetail, $ratio);
                }
            }
        }

        // Apply any taxes for shipping
        $shippingTaxAmount = $salesItem->getShippingTaxAmount();
        $originalShippingTaxAmount = $order->getShippingTaxAmount();
        if ($shippingTaxAmount && $originalShippingTaxAmount &&
            $shippingTaxAmount != 0 && floatval($originalShippingTaxAmount)
        ) {
            //An invoice or credit memo can have a different qty than its order
            $shippingRatio = $shippingTaxAmount / $originalShippingTaxAmount;
            $itemTaxDetails = $orderTaxDetails->getItems();
            foreach ($itemTaxDetails as $itemTaxDetail) {
                //Aggregate taxable items associated with shipping
                if ($itemTaxDetail->getType() == \Magento\Quote\Model\Quote\Address::TYPE_SHIPPING) {
                    $taxClassAmount = $this->_aggregateTaxes($taxClassAmount, $itemTaxDetail, $shippingRatio);
                }
            }
        }

        return $taxClassAmount;
    }

    private function _aggregateTaxes($taxClassAmount, OrderTaxDetailsItemInterface $itemTaxDetail, $ratio)
    {
        $itemAppliedTaxes = $itemTaxDetail->getAppliedTaxes();
        foreach ($itemAppliedTaxes as $itemAppliedTax) {
            $taxAmount = $itemAppliedTax->getAmount() * $ratio;
            $baseTaxAmount = $itemAppliedTax->getBaseAmount() * $ratio;

            if (0 == $taxAmount && 0 == $baseTaxAmount) {
                continue;
            }
            $taxCode = $itemAppliedTax->getCode();
            if (!isset($taxClassAmount[$taxCode])) {
                $taxClassAmount[$taxCode]['title'] = $itemAppliedTax->getTitle();
                $taxClassAmount[$taxCode]['percent'] = $itemAppliedTax->getPercent();
                $taxClassAmount[$taxCode]['tax_amount'] = $taxAmount;
                $taxClassAmount[$taxCode]['base_tax_amount'] = $baseTaxAmount;
            } else {
                $taxClassAmount[$taxCode]['tax_amount'] += $taxAmount;
                $taxClassAmount[$taxCode]['base_tax_amount'] += $baseTaxAmount;
            }
        }

        return $taxClassAmount;
    }
}