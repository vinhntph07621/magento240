<?php

namespace Omnyfy\Mcm\Model\ResourceModel\VendorPayoutHistory\Grid\Main;

use Magento\Framework\Api\Search\SearchResultInterface;
use Omnyfy\Mcm\Model\ResourceModel\VendorPayoutHistory\Collection as VendorPayoutHistoryCollection;

class Collection extends VendorPayoutHistoryCollection implements SearchResultInterface {

    protected function _construct() {
        $this->_init(
                'Magento\Framework\View\Element\UiComponent\DataProvider\Document', 'Omnyfy\Mcm\Model\ResourceModel\VendorPayoutHistory'
        );
        $this->addFilterToMap('vendor_name_status', 'name');
        $this->addFilterToMap('vendor_id', 'main_table.vendor_id');
        $this->addFilterToMap('payout_amount_currency', 'payout_amount');
        $this->addFilterToMap('ewallet_id', 'vp.ewallet_id');
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregations() {
        return $this->aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function setAggregations($aggregations) {
        $this->aggregations = $aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCriteria() {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null) {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount() {
        return $this->getSize();
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalCount($totalCount) {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(array $items = null) {
        return $this;
    }

    /**
     * @return $this|void
     */
    protected function _initSelect() {
        parent::_initSelect();
        $this->getSelect()->where('main_table.status IN (?)', [1, 4]); // Payout Status: 0 = Failed, 1 = Success, 3 = In progress, 4 = Processed - awaiting settlement
    }

}
