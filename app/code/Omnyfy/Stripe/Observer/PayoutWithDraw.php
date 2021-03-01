<?php

namespace Omnyfy\Stripe\Observer;

use Magento\Framework\Event\ObserverInterface;

class PayoutWithDraw implements ObserverInterface
{
    /**
     * @var \Omnyfy\Stripe\Model\ResourceModel\VendorConnectAccount
     */
    private $vendorConnectAccount;

    /**
     * @var Gateway
     */
    private $gatewayHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    private $eventManager;

    /**
     * @var \StripeIntegration\Payments\Helper\Subscriptions
     */
    private $subscriptionsHelper;

    /**
     * PayoutSend constructor.
     * @param \Omnyfy\Stripe\Model\ResourceModel\VendorConnectAccount $vendorConnectAccount
     * @param \Omnyfy\Stripe\Helper\Gateway $gatewayHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \StripeIntegration\Payments\Helper\Subscriptions $subscriptionsHelper
     */
    public function __construct(
        \Omnyfy\Stripe\Model\ResourceModel\VendorConnectAccount $vendorConnectAccount,
        \Omnyfy\Stripe\Helper\Gateway $gatewayHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \StripeIntegration\Payments\Helper\Subscriptions $subscriptionsHelper
    ) {
        $this->vendorConnectAccount = $vendorConnectAccount;
        $this->gatewayHelper = $gatewayHelper;
        $this->storeManager = $storeManager;
        $this->messageManager = $messageManager;
        $this->eventManager = $eventManager;
        $this->subscriptionsHelper = $subscriptionsHelper;
    }

    /**
     * Create Stripe payout for vendor m
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $baseCurrencyCode = $this->storeManager->getStore()->getBaseCurrency()->getCode();
        $eventData = $observer->getData('data');
        $stripeAccount = [
            'stripe_account' => $this->vendorConnectAccount->getStripeAccountIdByVendorId($eventData['ext_info']['vendor_id'])
        ];
        try {

            $payoutData = [
                'amount' => $this->subscriptionsHelper->convertMagentoAmountToStripeAmount($eventData['amount'],
                    $baseCurrencyCode),
                'currency' => $baseCurrencyCode
            ];
            $payoutResult = $this->gatewayHelper->createPayout($payoutData, $stripeAccount);

            if (!empty($payoutResult->id)) {
                $this->vendorConnectAccount->updateVendorWithdrawal(
                    $payoutResult->id,
                    $eventData['ext_info']
                );
            }
        } catch (\exception $e) {
            $this->eventManager->dispatch('omnyfy_payout_withdraw_fail', [
                'data' => [
                    'wallet_id' => $eventData['wallet_id'],
                    'account_ref' => $stripeAccount['stripe_account'],
                    'amount' => $eventData['amount'],
                    'ext_info' => json_encode($eventData['ext_info']),
                    'reason' => $e->getMessage()
                ]
            ]);
            $this->messageManager->addErrorMessage($e->getMessage());
        }
    }
}
