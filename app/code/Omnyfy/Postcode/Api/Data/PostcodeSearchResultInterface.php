<?php

namespace Omnyfy\Postcode\Api\Data;

use \Magento\Framework\Api\SearchResultsInterface;

interface PostcodeSearchResultInterface extends SearchResultsInterface
{

    /**
     * Get items
     * @return \Omnyfy\Postcode\Api\Data\PostcodeInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Omnyfy\Postcode\Api\Data\PostcodeInterface[] $items
     */
    public function setItems(array $items);

}
