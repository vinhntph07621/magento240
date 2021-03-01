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


namespace Mirasvit\Rewards\Model\Total\Quote;

use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Mirasvit\Rewards\Helper\Calculation;
use Mirasvit\Rewards\Model\Config\Source\Spending\ApplyTax;
use Mirasvit\Rewards\Model\Purchase;
use Mirasvit\RewardsAdminUi\Model\System\Config\Source\Spend\Method;
use Mirasvit\Rewards\Helper\Balance\Earn;
use Mirasvit\Rewards\Helper\Purchase as RewardPurchase;
use Mirasvit\Rewards\Model\Config;
use Mirasvit\Rewards\Service\Quote\Item\CalcPriceService;
use Mirasvit\Rewards\Service\RoundService;
use Mirasvit\Rewards\Service\ShippingService;
use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\Module\Manager;
use Magento\Tax\Model\TaxCalculation;
use Magento\Tax\Model\Config as TaxConfig;
use Magento\Customer\Model\Session;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Sales\Model\Order;
use Magento\Tax\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Discount extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var array
     */
    private $quoteItems = [];

    /**
     * @var array
     */
    private $quoteProductIds = [];

    /**
     * @var array
     */
    private $pointsInfo = [];

    /**
     * @var array
     */
    private $quoteItemsPrice = [];

    /**
     * @var AbstractItem
     */
    private $item;

    /**
     * @var \Magento\Quote\Model\Quote\Address
     */
    private $itemAddress;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    private $quote;

    /**
     * @var Purchase
     */
    private $purchase;

    /**
     * @var float
     */
    private $used = 0;

    /**
     * @var float
     */
    private $baseUsed = 0;

    /**
     * @var \Mirasvit\Rewards\Service\Quote\Item\CalcPriceService
     */
    private $calcPriceService;
    /**
     * @var \Mirasvit\Rewards\Model\Config
     */
    private $config;
    /**
     * @var \Mirasvit\Rewards\Helper\Balance\Earn
     */
    private $earnHelper;
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;
    /**
     * @var \Mirasvit\Rewards\Helper\Purchase
     */
    private $rewardsPurchase;
    /**
     * @var \Mirasvit\Rewards\Service\RoundService
     */
    private $roundService;
    /**
     * @var \Mirasvit\Rewards\Service\ShippingService
     */
    private $shippingService;
    /**
     * @var \Magento\Tax\Model\TaxCalculation
     */
    private $taxCalculation;
    /**
     * @var \Magento\Tax\Model\Config
     */
    private $taxConfig;
    /**
     * @var \Magento\Tax\Helper\Data
     */
    private $taxData;
    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var float
     */
    private $shippingTaxDiscount;
    /**
     * @var float
     */
    private $baseShippingTaxDiscount;

    private $productMetadata;

    private $checkoutSession;

    private $orderModel;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        Earn $earnHelper,
        RewardPurchase $rewardsPurchase,
        Config $config,
        CalcPriceService $calcPriceService,
        RoundService $roundService,
        ShippingService $shippingService,
        ProductMetadataInterface $productMetadata,
        Manager $moduleManager,
        TaxCalculation $taxCalculation,
        TaxConfig $taxConfig,
        Session $customerSession,
        CheckoutSession $checkoutSession,
        Order $orderModel,
        Data $taxData,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->earnHelper       = $earnHelper;
        $this->rewardsPurchase  = $rewardsPurchase;
        $this->config           = $config;
        $this->calcPriceService = $calcPriceService;
        $this->roundService     = $roundService;
        $this->shippingService  = $shippingService;
        $this->productMetadata  = $productMetadata;
        $this->moduleManager    = $moduleManager;
        $this->taxCalculation   = $taxCalculation;
        $this->taxConfig        = $taxConfig;
        $this->customerSession  = $customerSession;
        $this->checkoutSession  = $checkoutSession;
        $this->orderModel       = $orderModel;
        $this->taxData          = $taxData;
        $this->scopeConfig      = $scopeConfig;

        $this->setCode('rewards_discount');
    }

    /**
     * {@inheritdoc}
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        $this->purchase = null;// load purchase for each collect
        parent::collect($quote, $shippingAssignment, $total);

        $this->quote = $quote;

        /** @var \Magento\Quote\Model\Quote\Address $address */
        $address = $shippingAssignment->getShipping()->getAddress();

        $this->itemAddress = $address;

        $address->setDiscountDescription('');

        $purchase = $this->getPurchase();
        if (!$purchase) {
            return $this;
        }

        if ($this->config->getAdvancedSpendingCalculationMethod() == Method::METHOD_ITEMS) {

            $items = $shippingAssignment->getItems();
            if (!count($items)) {
                return $this;
            }
            /** calc earn points */
            $this->earnPoints();

            return $this;
        }

        $items = $shippingAssignment->getItems();
        if (!count($items)) {
            return $this;
        }
        $this->prepareItems();

        /** apply rewards discount */
        $this->spendPoints($total, $shippingAssignment);

        /** calc earn points */
        $this->earnPoints();

        return $this;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total            $total
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @return $this
     *
     * @throws \Exception
     */
    private function spendPoints($total, $shippingAssignment)
    {
        $purchase           = $this->getPurchase();
        $shippingAmount     = $this->shippingService->getSpendShippingPrice($total);
        $baseShippingAmount = $this->shippingService->getBaseSpendShippingPrice($total);

        $this->shippingService->setShippingAmount($baseShippingAmount);

        $purchase->setTotals($total);
        $purchase->calculateSpendingPoints();
        $this->resetCalcInfo();

        if ($purchase->getSpendAmount() <= 0 || empty($purchase->getQuoteProductIds())) {
            $this->processShipping($total);
            $this->earnPoints();
            $this->updateTotals($total);

            return $this;
        }
        $this->itemAddress->setDiscountDescription(__('Rewards Discount')->render());

        $items = $shippingAssignment->getItems();

        $this->quoteItemsPrice = $this->calcPriceService->getQuotePrices($items, $purchase);
        $this->quoteProductIds = $purchase->getQuoteProductIds();

        $this->calcPoints($items);
        $this->pointsInfo['shipping']     = $shippingAmount;
        $this->pointsInfo['baseShipping'] = $baseShippingAmount;

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($items as $item) {
            $this->item = $item;
            if (!$this->canProcess()) {
                continue;
            }
            $this->process();
        }

        /** Shipping discount */
        $this->processShipping($total);

        $this->baseUsed = $this->quote->getBaseItemsRewardsDiscount();
        $this->used     = $this->quote->getItemsRewardsDiscount();

        $this->fixDiscount();

        // we really need this
        $this->baseUsed = round($this->baseUsed, 2);
        $this->used     = round($this->used, 2);

        $this->updatePurchase();
        $this->updateTotals($total);

        return $this;
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    private function earnPoints()
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->customerSession->getCustomer();
        $this->earnHelper->setShippingAmount($this->getEarningShippingAmount());
        $earnedPoints = $this->earnHelper->getPointsEarned($this->quote, $customer);

        $purchase = $this->getPurchase();
        if ($earnedPoints != $purchase->getEarnPoints()) {
            $this->purchase->setEarnPoints($earnedPoints);
            $this->purchase->save();
        }
    }

    /**
     * We need this function because collection does not always return correct item with method getById(). For example, checkout/cart/add
     *
     * @return void
     */
    private function prepareItems()
    {
        foreach ($this->quote->getItemsCollection() as $item) {
            $this->quoteItems[$item->getId()] = $item;
        }
    }

    /**
     * @return float
     */
    private function getEarningShippingAmount()
    {
        return $this->shippingService->getBaseEarnShippingPrice($this->itemAddress);
    }

    /**
     * @return void
     */
    private function updatePurchase()
    {
        $purchase = $this->getPurchase();
        $purchase->setBaseSpendAmount($this->baseUsed);
        $purchase->setSpendAmount($this->used);
        $purchase->save();
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return void
     */
    private function updateTotals($total)
    {
        $total->setBaseTotalAmount($this->getCode(), -$this->baseUsed);
        $total->setTotalAmount($this->getCode(), -$this->used);
        $total->setBaseRewardsDiscount($this->baseUsed);
        $total->setRewardsDiscount($this->used);

        // some Magento versions do not include shipping on this phase, that is why we check $amount
        if ($total->getBaseGrandTotal() > 0) {
            $amount = $total->getBaseGrandTotal() - $this->baseUsed;
            $amount = $amount > 0 ? $amount : 0;
            $total->setBaseGrandTotal($amount);
        }
        if ($total->getGrandTotal() > 0) {
            $amount = $total->getGrandTotal() - $this->used;
            $amount = $amount > 0 ? $amount : 0;
            $total->setGrandTotal($amount);
        }

        if ($this->isShippingMinOrderSet($this->quote)) {
            $discountAmount = $this->itemAddress->getDiscountAmount();

            $amount = $this->itemAddress->getDiscountAmount() + -$this->used;
            $amount = $amount < 0 ? $amount : 0;
            $this->itemAddress->setDiscountAmount($amount);

            $baseDiscountAmount = $this->itemAddress->getBaseDiscountAmount();

            $amount = $this->itemAddress->getBaseDiscountAmount() + -$this->baseUsed;
            $amount = $amount < 0 ? $amount : 0;
            $this->itemAddress->setBaseDiscountAmount($amount);
        }
        // required for m2.1.0 --- it should be tested and removed or approved
        if (version_compare($this->productMetadata->getVersion(), "2.1.0", "=")) {
            if (!empty($amount)) {
                $this->itemAddress->setBaseSubtotalTotalInclTax($amount);
            }
        }

        // we need to recollect shipping rates to apply Minimum Order Amount of free shipping method
        $this->itemAddress->setCollectShippingRates(true);

        // prevent infinite loop for quote loading from cart
        if ($this->moduleManager->isEnabled('WebShopApps_MatrixRate')) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Magento\Checkout\Model\Cart $cart */
            $cart = $objectManager->get('\Magento\Checkout\Model\Cart');
            $cart->setQuote($this->quote);
        }

        // prevent infinite loop for quote loading from cart
        if ($this->moduleManager->isEnabled('MageWorx_StoreLocator')) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Magento\Checkout\Model\Session $cart */
            $cart = $objectManager->get('\Magento\Checkout\Model\Session');
            $cart->replaceQuote($this->quote);
        }

        if ($this->isShippingMinOrderSet($this->quote)) {
            $this->itemAddress->collectShippingRates();

            $this->itemAddress->setDiscountAmount($discountAmount);

            $this->itemAddress->setBaseDiscountAmount($baseDiscountAmount);
        }

        // m2.1.0 does reset address discount on each totals collect
        if (version_compare($this->productMetadata->getVersion(), '2.2.0', '>=')) {
            if (is_string($this->itemAddress->getRegion())) {
                // we need this to show message about min order amount
                $this->itemAddress->getResource()->save($this->itemAddress);
            } else {
                // some extensions convert region to object
                $region = $this->itemAddress->getRegion();
                $this->itemAddress->setRegion('');
                // we need this to show message about min order amount
                $this->itemAddress->save();
                $this->itemAddress->setRegion($region);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $result = null;
        if ($quote->getIsVirtual()) {
            $address = $quote->getBillingAddress();
        } else {
            $address = $quote->getShippingAddress();
        }

        $amount = $total->getBaseRewardsDiscount();
        if (!$amount) {
            $purchase = $this->rewardsPurchase->getByQuote($quote);
            if ($purchase) {
                $amount = $purchase->getSpendAmount();
            }
        }

        if ($amount != 0) {
            $result = [
                'code'  => $this->getCode(),
                'title' => __('Rewards Discount')->render(),
                'value' => -$amount,
                'area'  => 'footer',
            ];

            $address->addTotal($result);
        }

        return $result;
    }

    /**
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function processShipping($total)
    {
        if (!$this->canProcessShipping()) {
            return $this;
        }

        $purchase = $this->getPurchase();

        $currencyRate = 1;
        if (!$this->isCustomRoundingEnabled()) {
            $currencyRate = $this->quote->getBaseToQuoteRate();
        }

        $baseShippingSpendAmount = $purchase->getBaseSpendAmount() - $this->quote->getBaseItemsRewardsDiscount() -
            $this->quote->getBaseItemsRewardsTaxDiscount();
        if ($baseShippingSpendAmount <= Calculation::ZERO_VALUE) {
            $baseShippingSpendAmount = 0;
        }
        if ($baseShippingSpendAmount <= 0) {
            return $this;
        }

        $baseShippingDiscount = $baseShippingSpendAmount;
        $baseShippingDiscount += $this->fixBaseShippingTaxRounding();
        $shippingDiscount = $baseShippingDiscount * $currencyRate;
        $shippingDiscount += $this->fixShippingTaxRounding();
        $shippingDiscount = round($shippingDiscount, 2, PHP_ROUND_HALF_DOWN);
        $this->quote->setItemsRewardsDiscount($this->quote->getItemsRewardsDiscount() + $shippingDiscount);
        $this->quote->setBaseItemsRewardsDiscount($this->quote->getBaseItemsRewardsDiscount() + $baseShippingDiscount);

        $shippingDiscount += $this->itemAddress->getShippingDiscountAmount();
        $baseShippingDiscount += $this->itemAddress->getBaseShippingDiscountAmount();

        $this->itemAddress->setShippingDiscountAmount($shippingDiscount);
        $this->itemAddress->setBaseShippingDiscountAmount($baseShippingDiscount);

        if ($this->isApplyTaxAfterDiscount()) {
            $taxPercent = $this->getShippingTaxPercent();
            if ($taxPercent) {
                $this->shippingTaxDiscount     = round($shippingDiscount * $taxPercent / 100, 2);
                $this->baseShippingTaxDiscount = round($baseShippingDiscount * $taxPercent / 100, 2);
            }
        }
        $total->setShippingDiscountAmount($shippingDiscount);
        $total->setBaseShippingDiscountAmount($baseShippingDiscount);

        return $this;
    }

    /**
     * @return bool
     */
    private function isApplyTaxAfterDiscount()
    {
        return $this->taxConfig->applyTaxAfterDiscount() &&
            $this->config->getGeneralApplyTaxAfterSpendingDiscount() == ApplyTax::APPLY_SPENDING_TAX_DEFAULT;
    }

    /**
     * @return $this
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function process()
    {
        $rewardsPrices = $this->quoteItemsPrice[$this->item->getId()];
        $this->item->setRewardsTotalPrice($rewardsPrices['price']);
        $this->item->setRewardsBaseTotalPrice($rewardsPrices['basePrice']);

        if ($this->item->getRewardsTotalPrice() == 0) {
            return $this;
        }

        if ($this->pointsInfo['total'] == 0) {//protection from division on zero
            $this->pointsInfo['total'] = $this->item->getRewardsTotalPrice();
        }
        if ($this->pointsInfo['baseTotal'] == 0) {//protection from division on zero
            $this->pointsInfo['baseTotal'] = $this->item->getRewardsBaseTotalPrice();
        }

        $total    = $this->pointsInfo['total'];// - $this->pointsInfo['discount'];
        $spend    = $this->pointsInfo['spendAmount'];// - $this->pointsInfo['shipping'];
        $price    = $this->item->getRewardsTotalPrice();// - $this->item->getDiscountAmount();
        $discount = $price / $total * $spend;

        $baseTotal    = $this->pointsInfo['baseTotal'];// - $this->pointsInfo['baseDiscount'];
        $baseSpend    = $this->pointsInfo['baseSpendAmount'];// - $this->pointsInfo['baseShipping'];
        $basePrice    = $this->item->getRewardsBaseTotalPrice();// - $this->item->getBaseDiscountAmount();
        $baseDiscount = $basePrice / $baseTotal * $baseSpend;

        if ($discount > $this->item->getRewardsTotalPrice()) {
            $discount = $this->item->getRewardsTotalPrice();
        }
        if ($baseDiscount > $this->item->getRewardsBaseTotalPrice()) {
            $baseDiscount = $this->item->getRewardsBaseTotalPrice();
        }

        $itemsRewardsDiscount     = $this->quote->getItemsRewardsDiscount();
        $baseItemsRewardsDiscount = $this->quote->getBaseItemsRewardsDiscount();
        $this->quote->setItemsRewardsDiscount($itemsRewardsDiscount + $discount);
        $this->quote->setBaseItemsRewardsDiscount($baseItemsRewardsDiscount + $baseDiscount);

        $discount = $this->roundPriceWithFaonniPrice($discount);
        if (abs($this->quote->getBaseAwStoreCreditAmount()) > 0) {
            $discount     = round($discount, 2);
            $baseDiscount = round($baseDiscount, 2);
        }
        $this->quoteItems[$this->item->getId()]
            ->setRewardsDiscountAmount($discount)
            ->setBaseRewardsDiscountAmount($baseDiscount)
        ;

        $this->item->setRewardsDiscountAmount($discount);
        $this->item->setBaseRewardsDiscountAmount($baseDiscount);

        return $this;
    }

    /**
     * @return bool
     */
    private function canProcess()
    {
        if ($this->item->getParentItem()) {
            return false;
        }
        if (!in_array($this->item->getId(), $this->quoteProductIds)) {
            return false;
        }
        if (empty($this->quoteItemsPrice[$this->item->getId()])) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function canProcessShipping()
    {
        if (!$this->applyToShipping()) {
            return false;
        }

        return true;
    }

    /**
     * @param array $items
     * @return void
     */
    private function calcPoints($items)
    {
        $rewardsTotal = $rewardsBaseTotal = 0;
        $discount = $baseDiscount = 0;
        foreach ($items as $quoteItem) {
            if (in_array($quoteItem->getId(), $this->quoteProductIds) &&
                isset($this->quoteItemsPrice[$quoteItem->getId()])
            ) {
                $rewardsPrices = $this->quoteItemsPrice[$quoteItem->getId()];
                $rewardsTotal     += $rewardsPrices['price'];
                $rewardsBaseTotal += $rewardsPrices['basePrice'];
            }
            $discount     += $quoteItem->getDiscountAmount();
            $baseDiscount += $quoteItem->getBaseDiscountAmount();
        }
        $total     = $rewardsTotal;
        $baseTotal = $rewardsBaseTotal;

        $purchase        = $this->getPurchase();
        $baseSpendAmount = $purchase->getBaseSpendAmount();

        if (!$baseTotal) {
            $baseTotal = $total;
        }
        if ($baseSpendAmount > $baseTotal) {
            $baseSpendAmount = $baseTotal;
        }

        $currencyRate = 1;
        if ($baseTotal > 0 && !$this->isCustomRoundingEnabled()) { //for some reason subtotal can be 0
            $currencyRate = $total / $baseTotal;
        }
        $spendAmount = round($baseSpendAmount * $currencyRate, 2, PHP_ROUND_HALF_DOWN);

        $this->pointsInfo = [
            'tax'              => $this->itemAddress->getTaxAmount() +
                $this->itemAddress->getDiscountTaxCompensationAmount(),
            'baseTax'          => $this->itemAddress->getBaseTaxAmount() +
                $this->itemAddress->getBaseDiscountTaxCompensationAmount(),
            'total'            => $total,
            'baseTotal'        => $baseTotal,
            'discount'         => $discount,
            'baseDiscount'     => $baseDiscount,
            'spendAmount'      => $spendAmount,
            'baseSpendAmount'  => $baseSpendAmount,
            'currencyRate'     => $currencyRate,
            'spendPoints'      => $purchase->getSpendPoints(),
            'leftAmount'       => $spendAmount,
            'baseLeftAmount'   => $baseSpendAmount,
        ];
    }

    /**
     * We need this because Faonni_Price changes total without basetotal
     *
     * @return bool
     */
    private function isCustomRoundingEnabled()
    {
        $address = $this->itemAddress;
        return $this->moduleManager->isEnabled('Faonni_Price') &&
            $address->getQuote()->getBaseCurrencyCode() == $address->getQuote()->getQuoteCurrencyCode();
    }

    /**
     * @param float $price
     * @return float
     */
    private function roundPriceWithFaonniPrice($price)
    {
        if (!$this->moduleManager->isEnabled('Faonni_Price')) {
            return $price;
        }
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $helper        = $objectManager->create('Faonni\Price\Helper\Data');
        $math          = $objectManager->create('Faonni\Price\Model\Math');
        if (!$helper->isEnabled() ||
            !$helper->isRoundingDiscount()
        ) {
            return $price;
        }

        return $math->round($price);
    }

    /**
     * @return bool|Purchase
     */
    private function getPurchase()
    {
        /** start compatibility with Aheadgroups_Ordereditor */
        if ($this->checkoutSession->getCurrentOrderId() > 0) {
            $order = $this->orderModel->load((int)$this->checkoutSession->getCurrentOrderId());
            $this->purchase = $this->rewardsPurchase->getByOrder($order);

            return $this->purchase;
        }
        /** end compatibility with Aheadgroups_Ordereditor */
        if (empty($this->purchase) || $this->purchase->getQuote()->getId() != $this->quote->getId()) {
            $this->purchase = $this->rewardsPurchase->getByQuote($this->quote);
        }

        return $this->purchase;
    }

    /**
     * @return bool
     */
    private function applyToShipping()
    {
        return $this->config->getGeneralIsSpendShipping();
    }

    /**
     * @return float
     */
    private function getShippingTaxPercent()
    {
        $delta = 0;
        $taxes = $this->itemAddress->getItemsAppliedTaxes();
        if (empty($taxes['shipping'])) {
            return $delta;
        }
        $tax = array_shift($taxes['shipping']);

        return $tax['percent'];
    }

    /**
     * @return float
     */
    private function fixShippingTaxRounding()
    {
        $taxPercent  = $this->getShippingTaxPercent();
        $shippingTax = $this->shippingService->getSpendShippingPrice($this->itemAddress) * $taxPercent / 100;

        return abs($this->itemAddress->getShippingTaxAmount() - round($shippingTax, 2));
    }

    /**
     * @return float
     */
    private function fixBaseShippingTaxRounding()
    {
        $taxPercent  = $this->getShippingTaxPercent();
        $shippingTax = $this->shippingService->getBaseSpendShippingPrice($this->itemAddress) * $taxPercent / 100;

        return abs($this->itemAddress->getBaseShippingTaxAmount() - round($shippingTax, 2));
    }

    /**
     * @return void
     */
    private function resetCalcInfo()
    {
        $this->used     = 0;
        $this->baseUsed = 0;
        $this->quote->setItemsRewardsDiscount(0);
        $this->quote->setBaseItemsRewardsDiscount(0);
        $this->quote->setItemsRewardsTaxDiscount(0);
        $this->quote->setBaseItemsRewardsTaxDiscount(0);

        $this->purchase = null;
    }

    /**
     * @return void
     */
    private function fixDiscount()
    {
        $purchase = $this->getPurchase();
        if ($this->baseUsed > $purchase->getBaseSpendAmount()) {
            $currencyRate   = $this->quote->getBaseToQuoteRate();
            $this->baseUsed = $purchase->getBaseSpendAmount();
            $this->used     = $this->baseUsed * $currencyRate;
        }
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return bool
     */
    private function isShippingMinOrderSet($quote)
    {
        $enabled = $this->scopeConfig->getValue(
            'carriers/freeshipping/active',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $quote->getStore()->getWebsiteId()
        );
        $amount  = $this->scopeConfig->getValue(
            'carriers/freeshipping/free_shipping_subtotal',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $quote->getStore()->getWebsiteId()
        );

        return $enabled && $amount > 0;
    }
}
