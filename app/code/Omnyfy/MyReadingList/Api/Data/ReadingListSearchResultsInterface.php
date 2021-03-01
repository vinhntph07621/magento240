<?php


namespace Omnyfy\MyReadingList\Api\Data;

interface ReadingListSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get ReadingList list.
     * @return \Omnyfy\MyReadingList\Api\Data\ReadingListInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \Omnyfy\MyReadingList\Api\Data\ReadingListInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
