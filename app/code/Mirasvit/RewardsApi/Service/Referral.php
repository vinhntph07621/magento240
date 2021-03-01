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



namespace Mirasvit\RewardsApi\Service;

use Magento\Checkout\Model\Session;
use Mirasvit\Rewards\Model\Config;
use Mirasvit\Rewards\Model\ReferralFactory;
use Mirasvit\Rewards\Model\ResourceModel\ReferralLink\CollectionFactory;

class Referral
{
    private $checkoutSession;

    private $referralFactory;

    private $referralLinkCollectionFactory;

    public function __construct(
        Session $checkoutSession,
        ReferralFactory $referralFactory,
        CollectionFactory $referralLinkCollectionFactory
    ) {
        $this->checkoutSession               = $checkoutSession;
        $this->referralFactory               = $referralFactory;
        $this->referralLinkCollectionFactory = $referralLinkCollectionFactory;
    }

    /**
     * @param int $customerId
     *
     * @return string
     */
    public function getReferralCode($customerId)
    {
        $link = $this->referralLinkCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customerId)
            ->getFirstItem();

        //if we haven't generated link, create it
        if (!$link->getId()) {
            $link->createReferralLinkId($customerId);
        }

        return $link->getReferralLink();
    }

    /**
     * @param int    $customerId
     * @param string $code
     * @param int    $referrerCustomerId
     * @param int    $storeId
     *
     * @return int
     */
    public function addReferral($customerId, $code, $referrerCustomerId, $storeId)
    {
        $result      = 0;
        $refererCode = $this->getReferralCode($referrerCustomerId);
        if ($refererCode == $code) {
            $referral = $this->referralFactory->create()
                ->setCustomerId($referrerCustomerId)
                ->setNewCustomerId($customerId)
                ->setStatus(Config::REFERRAL_STATUS_SIGNUP)
                ->setStoreId($storeId)
                ->save();
            $result   = $referral->getId();
        }

        return $result;
    }

    /**
     * @param string $code
     * @param int    $quoteId
     * @param int    $storeId
     *
     * @return int
     */
    public function addGuestReferral($code, $quoteId, $storeId)
    {
        $result     = 0;
        $customerId = $this->getCustomerIdByCode($code);

        if ($customerId) {
            $referral = $this->referralFactory->create()
                ->setCustomerId($customerId)
                ->setStatus(Config::REFERRAL_STATUS_VISITED)
                ->setStoreId($storeId)
                ->setQuoteId($quoteId)
                ->save();

            $result = $referral->getId();

            $this->checkoutSession->setReferral($referral->getId());
        }

        return $result;
    }

    /**
     * @param string $code
     *
     * @return int
     */
    private function getCustomerIdByCode($code)
    {
        $link = $this->referralLinkCollectionFactory->create()
            ->addFieldToFilter('referral_link', $code)
            ->getFirstItem();

        return $link->getCustomerId() ? : 0;
    }
}
