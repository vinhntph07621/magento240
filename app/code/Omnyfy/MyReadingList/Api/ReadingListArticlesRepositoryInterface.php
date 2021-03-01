<?php


namespace Omnyfy\MyReadingList\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ReadingListArticlesRepositoryInterface
{

    /**
     * Save ReadingListArticles
     * @param \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface $readingListArticles
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface $readingListArticles
    );

    /**
     * Retrieve ReadingListArticles
     * @param string $readinglistarticlesId
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($readinglistarticlesId);

    /**
     * Retrieve ReadingListArticles matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete ReadingListArticles
     * @param \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface $readingListArticles
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface $readingListArticles
    );

    /**
     * Delete ReadingListArticles by ID
     * @param string $readinglistarticlesId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($readinglistarticlesId);
}