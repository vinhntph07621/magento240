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


namespace Mirasvit\RewardsApi\Model;

use Mirasvit\Rewards\Api\Repository\ReferralRepositoryInterface;
use Mirasvit\Rewards\Model\Config as Config;

class Referral implements ReferralRepositoryInterface
{
    private $referralService;

    public function __construct(
        \Mirasvit\RewardsApi\Service\Referral $referralService
    ) {
        $this->referralService = $referralService;
    }

    /**
     * @inheritDoc
     */
    public function getCode($customerId)
    {
        return $this->referralService->getReferralCode($customerId);
    }

    /**
     * @inheritDoc
     */
    public function addReferral($customerId, $code, $referrerCustomerId, $storeId)
    {
        return $this->referralService->addReferral($customerId, $code, $referrerCustomerId, $storeId);
    }

    /**
     * @inheritDoc
     */
    public function addGuestReferral($code, $quoteId, $storeId)
    {
        return $this->referralService->addGuestReferral($code, $quoteId, $storeId);
    }
}
