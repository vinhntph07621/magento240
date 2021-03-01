<?php


namespace Omnyfy\Checklist\Api\Data;

interface ChecklistItemsSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get ChecklistItems list.
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface[]
     */
    public function getItems();

    /**
     * Set checklist_item_id list.
     * @param \Omnyfy\Checklist\Api\Data\ChecklistItemsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
