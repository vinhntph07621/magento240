<?php

namespace Omnyfy\Core\Api;

use Omnyfy\Core\Api\Data\SimpleParameterSearchInterface;

class SimpleParameterSearch extends \Magento\Framework\Api\AbstractSimpleObject implements SimpleParameterSearchInterface
{

    const KEY_ITEMS = 'items';
    const KEY_TOTAL_COUNT = 'total_count';

    /**
     * Get items
     *
     * @return \Magento\Framework\Api\AbstractExtensibleObject[]
     */
    public function getItems()
    {
        return $this->_get(self::KEY_ITEMS) === null ? [] : $this->_get(self::KEY_ITEMS);
    }

    /**
     * Set items
     *
     * @param \Magento\Framework\Api\AbstractExtensibleObject[] $items
     * @return $this
     */
    public function setItems(array $items)
    {
        return $this->setData(self::KEY_ITEMS, $items);
    }

    /**
     * Get total count
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->_get(self::KEY_TOTAL_COUNT);
    }

    /**
     * Set total count
     *
     * @param int $count
     * @return $this
     */
    public function setTotalCount($count)
    {
        return $this->setData(self::KEY_TOTAL_COUNT, $count);
    }

}
