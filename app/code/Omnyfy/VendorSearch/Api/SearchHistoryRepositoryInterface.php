<?php


namespace Omnyfy\VendorSearch\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface SearchHistoryRepositoryInterface
{


    /**
     * Save SearchHistory
     * @param \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface $searchHistory
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface $searchHistory
    );

    /**
     * Retrieve SearchHistory
     * @param string $searchhistoryId
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($searchhistoryId);

    /**
     * Retrieve SearchHistory matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistorySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete SearchHistory
     * @param \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface $searchHistory
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface $searchHistory
    );

    /**
     * Delete SearchHistory by ID
     * @param string $searchhistoryId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($searchhistoryId);
}
