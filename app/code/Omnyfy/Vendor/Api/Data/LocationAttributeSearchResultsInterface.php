<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/9/17
 * Time: 10:19 AM
 */
namespace Omnyfy\Vendor\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface LocationAttributeSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Omnyfy\Vendor\Api\Data\LocationAttributeInterface[]
     */
    public function getItems();

    /**
     * @param \Omnyfy\Vendor\Api\Data\LocationAttributeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}