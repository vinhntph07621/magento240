<?php

namespace Omnyfy\Postcode\Api\Data;

use \Omnyfy\Core\Api\Data\SimpleParameterSearchInterface;

interface PostcodeSimpleParameterSearchInterface extends SimpleParameterSearchInterface
{

    /**
     * Get items
     *
     * @return \Omnyfy\Postcode\Api\Data\PostcodeInterface[]
     */
    public function getItems();

    /**
     * Set items
     *
     * @param \Omnyfy\Postcode\Api\Data\PostcodeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);

}
