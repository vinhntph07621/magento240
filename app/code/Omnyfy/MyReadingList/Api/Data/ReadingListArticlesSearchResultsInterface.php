<?php


namespace Omnyfy\MyReadingList\Api\Data;

interface ReadingListArticlesSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get ReadingListArticles list.
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface[]
     */
    public function getItems();

    /**
     * Set readinglist_id list.
     * @param \Omnyfy\MyReadingList\Api\Data\ReadingListArticlesInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}