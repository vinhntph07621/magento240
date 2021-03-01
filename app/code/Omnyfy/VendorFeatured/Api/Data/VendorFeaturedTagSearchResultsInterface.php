<?php


namespace Omnyfy\VendorFeatured\Api\Data;

interface VendorFeaturedTagSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{


    /**
     * Get vendor_featured_tag list.
     * @return \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface[]
     */
    public function getItems();

    /**
     * Set vendor_featured_id list.
     * @param \Omnyfy\VendorFeatured\Api\Data\VendorFeaturedTagInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
