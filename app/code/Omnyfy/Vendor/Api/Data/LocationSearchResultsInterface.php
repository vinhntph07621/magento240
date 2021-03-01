<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 10/7/17
 * Time: 2:22 PM
 */
namespace Omnyfy\Vendor\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface LocationSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get attributes list.
     *
     * @return \Omnyfy\Vendor\Api\Data\LocationInterface[]
     */
    public function getItems();

    /**
     * Set attributes list.
     *
     * @param \Omnyfy\Vendor\Api\Data\LocationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}