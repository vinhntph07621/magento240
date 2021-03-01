<?php


namespace Omnyfy\VendorSearch\Api\Data;

interface SearchHistorySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get SearchHistory list.
     * @return \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface[]
     */
    public function getItems();

    /**
     * Set location list.
     * @param \Omnyfy\VendorSearch\Api\Data\SearchHistoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
