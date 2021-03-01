<?php


namespace Omnyfy\MyReadingList\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ReadingListRepositoryInterface
{


    /**
     * Save ReadingList
     * @param \Omnyfy\MyReadingList\Api\Data\ReadingListInterface $readingList
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Omnyfy\MyReadingList\Api\Data\ReadingListInterface $readingList
    );

    /**
     * Retrieve ReadingList
     * @param string $readinglistId
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($readinglistId);

    /**
     * Retrieve ReadingList matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete ReadingList
     * @param \Omnyfy\MyReadingList\Api\Data\ReadingListInterface $readingList
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Omnyfy\MyReadingList\Api\Data\ReadingListInterface $readingList
    );

    /**
     * Delete ReadingList by ID
     * @param string $readinglistId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($readinglistId);
}
