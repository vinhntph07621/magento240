<?php

namespace Omnyfy\Mcm\Model\ResourceModel\MarketplaceCommissionReport\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Omnyfy\Mcm\Model\ResourceModel\MarketplaceCommissionReport\Collection as MarketplaceCommissionReportCollection;

class Collection extends MarketplaceCommissionReportCollection implements SearchResultInterface {

    public function _construct() {
        $this->_init(
                'Magento\Framework\View\Element\UiComponent\DataProvider\Document', 'Omnyfy\Mcm\Model\ResourceModel\MarketplaceCommissionReport'
        );
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
    public function getAllIds($limit = null, $offset = null) {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * Clone and reset collection
     *
     * @param null $limit
     * @param null $offset
     * @return Select
     */
    protected function _getAllIdsSelect($limit = null, $offset = null) {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
        $idsSelect->columns($this->_idFieldName);
        $idsSelect->limit($limit, $offset);
        return $idsSelect;
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
        $this->extendSelectQuery($aliase = 've', $self = 'se');
    }

    protected function _renderFiltersBefore() {
        $this->extendSelectQuery($aliase = 'vv', $self = 's');
        return parent::_renderFiltersBefore();
    }

    public function extendSelectQuery($aliase = 've', $self = 's') {
        $this->getSelect()->columns(
                array(
                    'category_fee' => new \Zend_Db_Expr('(SELECT SUM(total_category_fee + total_category_fee_tax) FROM omnyfy_mcm_vendor_order WHERE order_id=main_table.entity_id)'),
                    'seller_fee' => new \Zend_Db_Expr('(SELECT SUM(total_seller_fee + total_seller_fee_tax) FROM omnyfy_mcm_vendor_order WHERE order_id=main_table.entity_id)'),
                    'disbursement_fee' => new \Zend_Db_Expr('(SELECT SUM(disbursement_fee + disbursement_fee_tax) FROM omnyfy_mcm_vendor_order WHERE order_id=main_table.entity_id)'),
                    'gross_earnings' => '(mcm_transaction_fee_incl_tax+ (SELECT SUM(total_category_fee + total_category_fee_tax + total_seller_fee + total_seller_fee_tax + disbursement_fee + disbursement_fee_tax) FROM omnyfy_mcm_vendor_order WHERE order_id=main_table.entity_id))'
                )
        );
    }

}
