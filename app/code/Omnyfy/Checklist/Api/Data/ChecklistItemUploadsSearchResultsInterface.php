<?php


namespace Omnyfy\Checklist\Api\Data;

interface ChecklistItemUploadsSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get ChecklistItemUploads list.
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUploadsInterface[]
     */
    public function getItems();

    /**
     * Set upload_id list.
     * @param \Omnyfy\Checklist\Api\Data\ChecklistItemUploadsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
