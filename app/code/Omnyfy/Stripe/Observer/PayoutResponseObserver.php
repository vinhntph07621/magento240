<?php
namespace Omnyfy\Stripe\Observer;

use Magento\Framework\Event\ObserverInterface;

class PayoutResponseObserver implements ObserverInterface
{
    /**
     * @var \Omnyfy\Stripe\Model\ResourceModel\VendorConnectAccount
     */
    protected $vendorConnectAccount;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Omnyfy\Stripe\Helper\Data
     */
    protected $stripeHelper;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * PayoutResponseObserver constructor.
     * @param \Omnyfy\Stripe\Model\ResourceModel\VendorConnectAccount $vendorConnectAccount
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     */
    public function __construct(
        \Omnyfy\Stripe\Model\ResourceModel\VendorConnectAccount $vendorConnectAccount,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Omnyfy\Stripe\Helper\Data $stripeHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->vendorConnectAccount = $vendorConnectAccount;
        $this->eventManager = $eventManager;
        $this->stripeHelper = $stripeHelper;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $eventName = $observer->getEvent()->getName();
        $arrEvent = $observer->getData('arrEvent');
        $stdEvent = $observer->getData('stdEvent');
        $object = $observer->getData('object');
        $baseCurrencyCode = $this->storeManager->getStore()->getBaseCurrency()->getCode();
        $extInfo = $this->vendorConnectAccount->getExtInfoByStripePayout($object['id']);
        $vendorId = $this->vendorConnectAccount->getVendorIdByStripeAccountId($arrEvent['account']);
        switch ($eventName) {
            case "omnyfy_stripe_payments_webhook_payout_paid":
                $this->eventManager->dispatch('omnyfy_payout_withdraw_success', [
                    'data' => [
                        'wallet_id' => $arrEvent['account'],
                        'account_ref' => $arrEvent['account'],
                        'amount' => $this->stripeHelper->convertStripeAmountToMagentoAmount($object['amount'], $baseCurrencyCode),
                        'ext_info' => $extInfo
                    ]
                ]);
                break;
            case "omnyfy_stripe_payments_webhook_payout_failed":
                $this->eventManager->dispatch('omnyfy_payout_withdraw_fail', [
                    'data' => [
                        'wallet_id' => $arrEvent['account'],
                        'account_ref' => $arrEvent['account'],
                        'amount' => $this->stripeHelper->convertStripeAmountToMagentoAmount($object['amount'], $baseCurrencyCode),
                        'ext_info' => $extInfo,
                        'reason' => $object['failure_message']
                    ]
                ]);
                break;
            case "omnyfy_stripe_payments_webhook_account_updated":
                $this->eventManager->dispatch('omnyfy_vendorsignup_kyc_status_update',
                    $this->stripeHelper->processDataForAccountEvent($object, $vendorId, $arrEvent['account'])
                );
                break;
            default:
                break;
        }
    }
}
