<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-03
 * Time: 13:54
 */

namespace Omnyfy\Vendor\Api\Data;

interface VendorTypeSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Omnyfy\Vendor\Api\Data\VendorTypeInterface[]
     */
    public function getItems();

    /**
     * @param \Omnyfy\Vendor\Api\Data\VendorTypeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}