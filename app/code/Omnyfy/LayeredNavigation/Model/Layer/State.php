<?php

namespace Omnyfy\LayeredNavigation\Model\Layer;

use Omnyfy\LayeredNavigation\Model\Layer\Filter\Item;

class State extends \Magento\Framework\DataObject
{

    /**
     * Add filter item to layer state
     *
     * @param \Omnyfy\LayeredNavigation\Model\Layer\Filter\Item $filter
     * @return $this
     */
    public function addFilter($filter)
    {
        $filters = $this->getFilters();
        $filters[] = $filter;
        $this->setFilters($filters);
        return $this;
    }

    /**
     * Set layer state filter items
     *
     * @param \Omnyfy\LayeredNavigation\Model\Layer\Filter\Item[] $filters
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setFilters($filters)
    {
        if (!is_array($filters)) {
            throw new LocalizedException(__('The filters must be an array.'));
        }
        $this->setData('filters', $filters);
        return $this;
    }

    /**
     * Get applied to layer filter items
     *
     * @return \Omnyfy\LayeredNavigation\Model\Layer\Filter\Item[]
     */
    public function getFilters()
    {
        $filters = $this->getData('filters');
        if ($filters === null) {
            $filters = [];
            $this->setData('filters', $filters);
        }
        return $filters;
    }

}
