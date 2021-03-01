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



namespace Mirasvit\RewardsApi\Service\Customer\Management;

class Search implements \Mirasvit\Rewards\Api\Service\Customer\Management\SearchInterface
{
    /**
     * @var \Mirasvit\Rewards\Api\Repository\TransactionRepositoryInterface
     */
    private $transactionRepository;
    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    public function __construct(
        \Mirasvit\Rewards\Api\Repository\TransactionRepositoryInterface $transactionRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransactions($customerId, \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        if ($searchCriteria) {
            $customerSearchCriteria = $this->searchCriteriaBuilder
                ->addFilter('customer_id', $customerId)->create();
            $groups = $searchCriteria->getFilterGroups();
            foreach ($customerSearchCriteria->getFilterGroups() as $filterGroup) {
                $groups[] = $filterGroup;
            }
            $searchCriteria->setFilterGroups($groups);
        } else {
            $searchCriteria = $this->searchCriteriaBuilder
                ->addFilter('customer_id', $customerId)->create();
        }

        return $this->transactionRepository->getList($searchCriteria)->getItems();
    }
}