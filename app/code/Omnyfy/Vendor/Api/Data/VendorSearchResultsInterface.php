<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 10/7/17
 * Time: 2:35 PM
 */

namespace Omnyfy\Vendor\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface VendorSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Omnyfy\Vendor\Api\Data\VendorInterface[]|\Omnyfy\Vendor\Api\Data\LocationInterface[]
     */
    public function getItems();

    /**
     * @param \Omnyfy\Vendor\Api\Data\VendorInterface[]|\Omnyfy\Vendor\Api\Data\LocationInterface $items
     * @return $this
     */
    public function setItems(array $items);
}