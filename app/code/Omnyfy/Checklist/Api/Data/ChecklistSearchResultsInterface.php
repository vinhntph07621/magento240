<?php


namespace Omnyfy\Checklist\Api\Data;

interface ChecklistSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get Checklist list.
     * @return \Omnyfy\Checklist\Api\Data\ChecklistInterface[]
     */
    public function getItems();

    /**
     * Set checklist_id list.
     * @param \Omnyfy\Checklist\Api\Data\ChecklistInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
