<?php

namespace Omnyfy\Vendor\Api\Data;

use \Omnyfy\Core\Api\Data\SimpleParameterSearchInterface;

interface LocationSimpleParameterSearchInterface extends SimpleParameterSearchInterface
{

    /**
     * Get items
     *
     * @return \Omnyfy\Vendor\Api\Data\LocationInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Omnyfy\Vendor\Api\Data\LocationInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

}
