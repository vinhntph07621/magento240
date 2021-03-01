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


namespace Mirasvit\RewardsCheckout\Plugin\ThirdParty;

use \Magento\Checkout\Model\Session as CheckoutSession;;
use Mirasvit\Rewards\Helper\Purchase as PurchaseHelper;

/**
 * Apply rewards discount to Amasty shipping restriction rate calculations
 *
 * @package Mirasvit\Rewards\Plugin
 */
class AmastyShippingTableRatesAddRewardsDiscount
{
    /**
     * @var CheckoutSession
     */
    private $checkoutSession;
    /**
     * @var PurchaseHelper
     */
    private $purchaseHelper;

    public function __construct(
        CheckoutSession $checkoutSession,
        PurchaseHelper $purchaseHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->purchaseHelper  = $purchaseHelper;
    }

    /**
     * @param \Amasty\ShippingTableRates\Model\Rate       $rate
     * @param \callable  $proceed
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundInitTotals(\Amasty\ShippingTableRates\Model\Rate $rate, $proceed)
    {
        \Magento\Framework\Profiler::start(__CLASS__.':'.__METHOD__);
        $returnValue = $proceed();

        if (!$this->checkoutSession->getQuote()) {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);

            return $returnValue;
        }
        $purchase = $this->purchaseHelper->getByQuote($this->checkoutSession->getQuote());

        if (!$purchase) {
            \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);

            return $returnValue;
        }
        $returnValue['not_free_price'] = -$purchase->getSpendAmount();

        \Magento\Framework\Profiler::stop(__CLASS__.':'.__METHOD__);

        return $returnValue;
    }
}