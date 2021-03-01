<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-08
 * Time: 16:45
 */
namespace Omnyfy\Vendor\Model\Resource\VendorType\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;

class Collection extends \Omnyfy\Vendor\Model\Resource\VendorType\Collection implements SearchResultInterface
{
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
 