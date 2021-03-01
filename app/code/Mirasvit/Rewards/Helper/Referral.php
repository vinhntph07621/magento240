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



namespace Mirasvit\Rewards\Helper;

use Mirasvit\Rewards\Model\Config as Config;
use Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Store\Model\StoreFactory;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\RewardsApi\Service\Referral as ReferralService;
use Mirasvit\Rewards\Model\ReferralFactory;
use Mirasvit\Rewards\Model\ResourceModel\Referral\CollectionFactory;

class Referral extends AbstractHelper
{
    private $referralService;

    private $referralFactory;

    private $customerFactory;

    private $storeFactory;

    private $referralCollectionFactory;

    private $checkoutSession;

    private $session;

    private $rewardsBehavior;

    private $sessionManager;

    private $storeManager;

    private $context;

    public function __construct(
        CustomerFactory $customerFactory,
        CheckoutSession $checkoutSession,
        Session $session,
        StoreFactory $storeFactory,
        StoreManagerInterface $storeManager,
        SessionManagerInterface $sessionManager,
        ReferralService $referralService,
        BehaviorRule $rewardsBehavior,
        ReferralFactory $referralFactory,
        CollectionFactory $referralCollectionFactory,
        Context $context
    ) {
        $this->customerFactory           = $customerFactory;
        $this->checkoutSession           = $checkoutSession;
        $this->session                   = $session;
        $this->storeFactory              = $storeFactory;
        $this->storeManager              = $storeManager;
        $this->sessionManager            = $sessionManager;
        $this->referralService           = $referralService;
        $this->rewardsBehavior           = $rewardsBehavior;
        $this->referralFactory           = $referralFactory;
        $this->referralCollectionFactory = $referralCollectionFactory;
        $this->context                   = $context;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Customer\Model\Customer $customer
     * @param array                            $invitations
     * @param string                           $message
     *
     * @return array
     */
    public function frontendPost($customer, $invitations, $message)
    {
        $referrals = $this->referralCollectionFactory->create()
            ->addFieldToFilter("customer_id", $customer->getId())
            ->addFieldToFilter("created_at", ["gt" => new \Zend_Db_Expr('DATE_SUB(NOW(), INTERVAL 1 DAY)')]);
        if ($referrals->count() > 100) { //protection
            return [];
        }

        $rejectedEmails = [];
        foreach ($invitations as $email => $name) {
            $referrals = $this->referralCollectionFactory->create()
                ->addFieldToFilter('email', $email);
            if ($referrals->count()) {
                $rejectedEmails[] = $email;
                continue;
            }

            $message = nl2br(strip_tags($message));

            /** @var  \Mirasvit\Rewards\Model\Referral $referral */
            $referral = $this->referralFactory->create()
                ->setName($name)
                ->setEmail($email)
                ->setCustomerId($customer->getId())
                ->setStoreId($this->storeManager->getStore()->getId())
                ->save();
            $referral->sendInvitation($message);

            $this->rewardsBehavior->processRule(Config::BEHAVIOR_TRIGGER_SEND_LINK, $customer, $this->storeManager->getWebsite()->getId(), $email);
        }

        return $rejectedEmails;
    }

    /**
     * Remember refrerer when customer adds product to cart.
     *
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return void
     * @todo check that this function is needed
     */
    public function rememberReferal($quote)
    {
        //if we have referral, we save quote id
        if ($id = (int)$this->sessionManager->getReferral()) {
            $referral = $this->referralFactory->create()->load($id);
            if (!$referral->getQuoteId()) {
                $referral->setQuoteId($quote->getId());
                $referral->save();
            }
        }
    }

    /**
     * Find possible \Mirasvit\Rewards\Model\Referral for this order.
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return \Mirasvit\Rewards\Model\Referral|bool
     */
    public function loadReferral($order)
    {
        $quoteId   = $order->getQuoteId();

        $referrals = $this->referralCollectionFactory->create()
            ->addFieldToFilter('quote_id', $quoteId);
        if ($referrals->count()) {
            return $referrals->getFirstItem();
        }

        $referrals = $this->referralCollectionFactory->create()
            ->addFieldToFilter('email', $order->getCustomerEmail());
        if ($referrals->count()) {
            return $referrals->getFirstItem();
        }

        if ($id = (int)$this->sessionManager->getReferral()) {
            return $this->referralFactory->create()->load($id);
        }

        if ($id = (int)$this->checkoutSession->getReferral()) {
            return $this->referralFactory->create()->load($id);
        }

        $customerId = $order->getCustomerId();
        $referrals  = $this->referralCollectionFactory->create()
            ->addFieldToFilter('new_customer_id', $customerId);
        if ($referrals->count()) {
            return $referrals->getFirstItem();
        }

        return false;
    }

    /**
     * Customer A refers customer B. Customer B has placed this order.
     * This function can give points to customer A.
     *
     * @param \Magento\Sales\Model\Order $order
     *
     * @return void
     * @todo replace with \Mirasvit\Rewards\Service\Order\Transaction\Earn\Referral
     */
    public function processReferralOrder($order)
    {
        if (!$referral = $this->loadReferral($order)) {
            return;
        }

        /* @var  \Magento\Customer\Model\Customer $customer - customer A */
        if ($customerId = $order->getCustomerId()) {
            $customer = $this->customerFactory->create()->load($customerId);
        } else {
            $customer = new \Magento\Framework\DataObject();
            $customer->setIsGuest(true)
                ->setEmail($order->getCustomerEmail())
                ->setFirstname($order->getCustomerFirstname())
                ->setLastname($order->getCustomerLastname());
        }

        $websiteId   = $this->storeFactory->create()->load($order->getStoreId())->getWebsiteId();
        $transaction = $this->rewardsBehavior->processRule(
            Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER,
            $referral->getCustomerId(),
            $websiteId,
            $order->getId(),
            ['referred_customer' => $customer, 'order' => $order]
        );

        if ($transaction) {
            $referral->setQuoteId($order->getQuoteId())
                ->save();
        }

        $referral->finish(Config::BEHAVIOR_TRIGGER_REFERRED_CUSTOMER_ORDER, $customerId, $transaction);
    }

    /**
     * @return string
     */
    public function getReferralLinkId()
    {
        $customerId = $this->session->getCustomerId();

        return $this->referralService->getReferralCode($customerId);
    }
}
