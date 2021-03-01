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



namespace Mirasvit\Rewards\Service\Customer;

use Mirasvit\Rewards\Api\Data\TierInterface;
use Mirasvit\Rewards\Model\Config;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;

class Tier implements \Mirasvit\Rewards\Api\Service\Customer\TierInterface
{
    /**
     * @var array
     */
    private $customers = [];

    /**
     * @var array
     */
    private $customerTiers = [];
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Magento\Framework\Api\SortOrderBuilder
     */
    private $sortOrderBuilder;
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;
    /**
     * @var \Mirasvit\Rewards\Api\Repository\TierRepositoryInterface
     */
    private $tierRepository;
    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;
    /**
     * @var \Mirasvit\Rewards\Helper\Balance
     */
    private $balanceHelper;
    /**
     * @var \Mirasvit\Rewards\Helper\BehaviorRule
     */
    private $rewardsBehavior;
    /**
     * @var \Mirasvit\Rewards\Helper\Mail
     */
    private $rewardsMail;
    /**
     * @var \Mirasvit\Rewards\Helper\Tier\Order
     */
    private $tierOrder;
    /**
     * @var Config
     */
    private $config;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\SortOrderBuilder $sortOrderBuilder,
        \Magento\Framework\Registry $registry,
        \Mirasvit\Rewards\Api\Repository\TierRepositoryInterface $tierRepository,
        \Mirasvit\Rewards\Helper\Balance $balanceHelper,
        \Mirasvit\Rewards\Helper\BehaviorRule $rewardsBehavior,
        \Mirasvit\Rewards\Helper\Mail $rewardsMail,
        \Mirasvit\Rewards\Helper\Tier\Order $tierOrder,
        Config $config
    ) {
        $this->customerRepository = $customerRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->registry = $registry;
        $this->tierRepository = $tierRepository;
        $this->customerFactory = $customerFactory;
        $this->balanceHelper = $balanceHelper;
        $this->rewardsBehavior = $rewardsBehavior;
        $this->rewardsMail = $rewardsMail;
        $this->tierOrder = $tierOrder;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextTier($customerId)
    {
        $customer = $this->getCustomer($customerId);
        $days = $this->config->getTierCalculationPeriod();
        $website = $customer->getStore()->getWebsite();

        if ($this->config->getTierCalcType($website) == TierInterface::TYPE_ORDER) {
            $points = $this->tierOrder->getSumForLastDays($customer, $days);
        } else {
            $points = $this->balanceHelper->getPointsForLastDays($customerId, $days);
        }

        $sortOrderSort = $this->sortOrderBuilder
            ->setField(TierInterface::KEY_MIN_EARN_POINTS)
            ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(TierInterface::KEY_IS_ACTIVE, 1)
            ->addFilter('website_id', $customer->getWebsiteId())
            ->addFilter(TierInterface::KEY_MIN_EARN_POINTS, $points, 'gt')
            ->addSortOrder($sortOrderSort);

        $items = $this->tierRepository->getList($searchCriteria->create())->getItems();

        return array_shift($items);
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerTiers($customerId)
    {
        if (empty($this->customerTiers[$customerId])) {
            $customer = $this->getCustomer($customerId);
            $sortOrderSort = $this->sortOrderBuilder
                ->setField(TierInterface::KEY_MIN_EARN_POINTS)
                ->setDirection(\Magento\Framework\Api\SortOrder::SORT_ASC)
                ->create();

            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter(TierInterface::KEY_IS_ACTIVE, 1)
                ->addFilter('website_id', $customer->getWebsiteId())
                ->addSortOrder($sortOrderSort);

            $this->customerTiers[$customerId] = $this->tierRepository->getList($searchCriteria->create())->getItems();
        }

        return $this->customerTiers[$customerId];
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerTier($customerId)
    {
        $customer = $this->getCustomer($customerId);
        $tierId = $customer->getData(TierInterface::CUSTOMER_KEY_TIER_ID);

        try {
            $tier = $this->tierRepository->get($tierId);
        } catch (NoSuchEntityException $e) {
            $tier = false;
        }
        if (!$tier) {
            try {
                $tier = $this->tierRepository->getFirstTier();
            } catch (NoSuchEntityException $e) {
                $tier = false;
            }
        }

        return $tier;
    }

    /**
     * @param int $customerId
     * @return TierInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCurrentCustomerTier($customerId)
    {
        $days = $this->config->getTierCalculationPeriod();
        $customer = $this->getCustomer($customerId);
        $website = $customer->getStore()->getWebsite();
        if ($this->config->getTierCalcType($website) == TierInterface::TYPE_ORDER) {
            $points = $this->tierOrder->getSumForLastDays($customer, $days);
        } else {
            $points = $this->balanceHelper->getPointsForLastDays($customerId, $days);
        }
        if ($points < 0) {
            $points = 0;
        }

        $sortOrderSort = $this->sortOrderBuilder
            ->setField(TierInterface::KEY_MIN_EARN_POINTS)
            ->setDirection(\Magento\Framework\Api\SortOrder::SORT_DESC)
            ->create();

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(TierInterface::KEY_IS_ACTIVE, 1)
            ->addFilter('website_id', $customer->getWebsiteId())
            ->addFilter(TierInterface::KEY_MIN_EARN_POINTS, $points, 'lteq')
            ->addSortOrder($sortOrderSort);

        $items = $this->tierRepository->getList($searchCriteria->create())->getItems();
        if (!$items) {
            throw new LocalizedException(
                __('Unable to find tier for customer "%1". Please check tier configuration.', $customer->getName())
            );
        }

        return array_shift($items);
    }

    /**
     * @return $this
     */
    private function reset()
    {
        $this->customers = [];
        $this->customerTiers = [];

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function updateCustomerTier($customerId)
    {
        $this->reset();
        try {
            $newTier = $this->getCurrentCustomerTier($customerId);
            $currentTier = $this->getCustomerTier($customerId);
        } catch (\Exception $e) {
            return false;
        }

        if ($this->isUpdateTierAllowed($newTier, $currentTier)) {
            $customer = $this->customerRepository->getById($customerId);
            // added to correctly load customer (by website id) in other case, it send newsletter confirmation to all subscribers
            $customer = $this->customerRepository->get($customer->getEmail(), $customer->getWebsiteId());
            $customer->setCustomAttribute(TierInterface::CUSTOMER_KEY_TIER_ID, $newTier->getId());

            $this->customerRepository->save($customer);

            /** @var \Magento\Customer\Model\Customer $customer */
            if ($currentTier && !$this->registry->registry('rule_processing')) {
                if ($currentTier->getMinEarnPoints() < $newTier->getMinEarnPoints()) {
                    $customer = $this->getCustomer($customerId);
                    $this->rewardsMail->sendNotificationTierUpEmail($newTier, $customer);
                    $this->registry->register('rule_processing', true, true);
                    $this->rewardsBehavior->processRule(
                        Config::BEHAVIOR_TRIGGER_CUSTOMER_TIER_UP,
                        $customer,
                        $customer->getWebsiteId(),
                        $customerId . '-' . $newTier->getId() . '_' . time()
                    );
                    $this->registry->register('rule_processing', false, true);
                } elseif ($currentTier->getId() != $newTier->getId()) {
                    $this->registry->register('rule_processing', true, true);
                    $this->rewardsBehavior->processRule(
                        Config::BEHAVIOR_TRIGGER_CUSTOMER_TIER_DOWN,
                        $customer,
                        $customer->getWebsiteId(),
                        $customerId . '-' . $newTier->getId() . '_' . time()
                    );
                    $this->registry->register('rule_processing', false, true);
                }
            }
        }

        return $newTier;
    }

    /**
     * @param \Mirasvit\Rewards\Api\Data\TierInterface $newTier
     * @param \Mirasvit\Rewards\Api\Data\TierInterface $currentTier
     * @return bool
     */
    protected function isUpdateTierAllowed($newTier, $currentTier)
    {
        if (
            !$currentTier ||
            ($currentTier->getId() != $newTier->getId() && $this->config->getTierAutoMoveDown()) ||
            ($newTier->getMinEarnPoints() > $currentTier->getMinEarnPoints())
        ) {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getRemainingPoints($tier, $customerId)
    {
        $pointsLeft = 1;
        $days = $this->config->getTierCalculationPeriod();
        $customer = $this->getCustomer($customerId);
        $website = $customer->getStore()->getWebsite();
        if ($this->config->getTierCalcType($website) == TierInterface::TYPE_ORDER) {
            $balance = $this->tierOrder->getSumForLastDays($customer, $days);
        } else {
            $balance = $this->balanceHelper->getPointsForLastDays($customerId, $days);
        }
        $nextTierStartedAt = $tier->getMinEarnPoints();
        if ($tier && $nextTierStartedAt > $balance) {
            $pointsLeft = $nextTierStartedAt - $balance;
        }

        return $pointsLeft;
    }

    /**
     * @param int $customerId
     * @return \Magento\Customer\Model\Customer
     */
    protected function getCustomer($customerId)
    {
        if (!isset($this->customers[$customerId])) {
            $customer = $this->customerFactory->create();
            $customer->getResource()->load($customer, $customerId);
            $this->customers[$customerId] = $customer;
        }

        return $this->customers[$customerId];
    }
}