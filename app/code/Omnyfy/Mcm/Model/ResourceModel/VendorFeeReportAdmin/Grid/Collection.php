<?php

namespace Omnyfy\Mcm\Model\ResourceModel\VendorFeeReportAdmin\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Omnyfy\Mcm\Model\ResourceModel\VendorFeeReportAdmin\Collection as VendorFeeReportAdminCollection;
use Magento\Framework\DB\Select;

class Collection extends VendorFeeReportAdminCollection implements SearchResultInterface {

    public function _construct() {
        $this->_init(
                'Magento\Framework\View\Element\UiComponent\DataProvider\Document', 'Omnyfy\Mcm\Model\ResourceModel\VendorFeeReportAdmin'
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
        $ObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $context = $ObjectManager->get('Magento\Backend\Model\Session');

        $vendorInfo = $context->getVendorInfo();

        if (!empty($vendorInfo)) {
            $this->getSelect()->where('main_table.vendor_id=?', $vendorInfo['vendor_id']);
        } else {
            $this->getSelect()->where('(main_table.item_id IS NULL AND main_table.vendor_id IS NULL) OR (main_table.item_id IS NOT NULL AND main_table.vendor_id IS NOT NULL)');
        }
    }

    protected function _renderFiltersBefore() {
        $ObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $context = $ObjectManager->get('Magento\Backend\Model\Session');

        $vendorInfo = $context->getVendorInfo();
        if (!empty($vendorInfo)) {
            $this->getSelect()->where('main_table.vendor_id=?', $vendorInfo['vendor_id']);
        } else {
            $this->getSelect()->where('(main_table.item_id IS NULL AND main_table.vendor_id IS NULL) OR (main_table.item_id IS NOT NULL AND main_table.vendor_id IS NOT NULL)');
        }

        return parent::_renderFiltersBefore();
    }

    public function extendSelectQuery($aliase = 've', $self = 's') {

        $ObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $context = $ObjectManager->get('Magento\Backend\Model\Session');

        $vendorInfo = $context->getVendorInfo();

        if (!empty($vendorInfo)) {
            $this->getSelect()->where('main_table.vendor_id=?', $vendorInfo['vendor_id']);
        } else {
            $this->getSelect()->where('(main_table.item_id IS NULL AND main_table.vendor_id IS NULL) OR (main_table.item_id IS NOT NULL AND main_table.vendor_id IS NOT NULL)');
        }
    }

}
