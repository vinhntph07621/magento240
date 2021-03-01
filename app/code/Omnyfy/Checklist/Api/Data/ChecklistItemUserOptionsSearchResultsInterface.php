<?php


namespace Omnyfy\Checklist\Api\Data;

interface ChecklistItemUserOptionsSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get ChecklistItemUserOptions list.
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface[]
     */
    public function getItems();

    /**
     * Set user_option_id list.
     * @param \Omnyfy\Checklist\Api\Data\ChecklistItemUserOptionsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
