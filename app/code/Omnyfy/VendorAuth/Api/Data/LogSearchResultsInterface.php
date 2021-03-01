<?php


namespace Omnyfy\VendorAuth\Api\Data;

interface LogSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get log list.
     * @return \Omnyfy\VendorAuth\Api\Data\LogInterface[]
     */
    public function getItems();

    /**
     * Set loggedin_vendor_id list.
     * @param \Omnyfy\VendorAuth\Api\Data\LogInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}