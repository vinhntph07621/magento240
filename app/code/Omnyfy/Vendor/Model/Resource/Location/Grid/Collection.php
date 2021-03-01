<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 10/11/17
 * Time: 10:58 AM
 */
namespace Omnyfy\Vendor\Model\Resource\Location\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;


class Collection extends \Omnyfy\Vendor\Model\Resource\Location\Collection implements SearchResultInterface
{


    protected function _initSelect()
    {
        parent::_initSelect();
        $this->joinVendorInfo();
        return $this;
    }

    public function getAggregations()
    {
        return $this->aggregations;
    }

    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    public function getSearchCriteria()
    {
        return null;
    }

    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria=null)
    {
        return $this;
    }

    public function getTotalCount()
    {
        return $this->getSize();
    }

    public function setTotalCount($totalCount)
    {
        return $this;
    }

    public function setItems(array $items=null)
    {
        return $this;
    }
}