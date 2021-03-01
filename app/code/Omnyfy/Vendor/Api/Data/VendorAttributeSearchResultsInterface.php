<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 11/9/17
 * Time: 12:07 PM
 */
namespace Omnyfy\Vendor\Api\Data;

interface VendorAttributeSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Omnyfy\Vendor\Api\Data\VendorAttributeInterface[]
     */
    public function getItems();

    /**
     * @param \Omnyfy\Vendor\Api\Data\VendorAttributeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}