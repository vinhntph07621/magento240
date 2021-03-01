<?php


namespace Omnyfy\VendorFeatured\Api\Data;

interface VendorTagSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get vendor_tag list.
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorTagInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Omnyfy\VendorFeatured\Api\Data\VendorTagInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
