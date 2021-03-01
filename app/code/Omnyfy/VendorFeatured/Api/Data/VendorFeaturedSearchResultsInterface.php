<?php


namespace Omnyfy\VendorFeatured\Api\Data;

interface VendorFeaturedSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get vendor_featured list.
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface[]
     */
    public function getItems();

    /**
     * Set vendor_id list.
     * @param \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
