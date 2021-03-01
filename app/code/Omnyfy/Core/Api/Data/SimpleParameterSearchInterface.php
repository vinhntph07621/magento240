<?php

namespace Omnyfy\Core\Api\Data;

use \Magento\Framework\Api\SearchResultsInterface;

interface SimpleParameterSearchInterface
{

    /**
     * Get items
     *
     * @return \Magento\Framework\Api\ExtensibleDataInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount();

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount);

}
