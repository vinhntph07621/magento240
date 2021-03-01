<?php


namespace Omnyfy\Checklist\Api\Data;

interface ChecklistItemOptionsSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get ChecklistItemOptions list.
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface[]
     */
    public function getItems();

    /**
     * Set option_id list.
     * @param \Omnyfy\Checklist\Api\Data\ChecklistItemOptionsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
