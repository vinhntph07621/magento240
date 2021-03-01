<?php


namespace Omnyfy\Checklist\Api\Data;

interface ChecklistItemUserUploadsSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get ChecklistItemUserUploads list.
     * @return \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface[]
     */
    public function getItems();

    /**
     * Set upload_is list.
     * @param \Omnyfy\Checklist\Api\Data\ChecklistItemUserUploadsInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
