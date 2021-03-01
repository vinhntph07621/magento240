<?php

namespace Omnyfy\Mcm\Model\ResourceModel\VendorOrder\Grid\MarketplaceEarnings;

use Magento\Framework\Api\Search\SearchResultInterface;
use Omnyfy\Mcm\Model\ResourceModel\VendorOrder\Collection as VendorOrderCollection;
use Magento\Framework\DB\Select;

class Collection extends VendorOrderCollection implements SearchResultInterface {

    protected function _construct() {
        $this->_init(
                'Magento\Framework\View\Element\UiComponent\DataProvider\Document', 'Omnyfy\Mcm\Model\ResourceModel\VendorOrder'
        );
        $this->addFilterToMap('vendor_name_status', 'name');
        $this->addFilterToMap('vendor_name', 'name');
        $this->addFilterToMap('vendor_id', 'main_table.vendor_id');
        $this->addFilterToMap('total_category_fee_incl_tax ', new \Zend_Db_Expr('SUM(total_category_fee + total_category_fee_tax)'));
        $this->addFilterToMap('total_seller_fee_incl_tax ', new \Zend_Db_Expr('SUM(total_seller_fee + total_seller_fee_tax)'));
        $this->addFilterToMap('total_disbursement_fee_incl_tax ', new \Zend_Db_Expr('SUM(disbursement_fee + disbursement_fee_tax)'));
        $this->addFilterToMap('grand_total_with_shipping ', new \Zend_Db_Expr('(SUM(main_table.base_grand_total + main_table.base_shipping_amount + main_table.base_shipping_tax - main_table.shipping_discount_amount) + so.mcm_base_transaction_fee_incl_tax)'));
        $this->addFilterToMap('mo_total_earning_amount ', new \Zend_Db_Expr('((SUM(total_category_fee + total_category_fee_tax + total_seller_fee + total_seller_fee_tax + disbursement_fee + disbursement_fee_tax) + so.mcm_base_transaction_fee_incl_tax)'));
    }

    public function addFieldToFilter($field, $condition = null) {

        if ($field == 'total_category_fee_incl_tax' || $field == 'total_seller_fee_incl_tax' || $field == 'total_disbursement_fee_incl_tax' || $field == 'grand_total_with_shipping' || $field == 'mo_total_earning_amount') {
            if (is_array($field)) {
                $conditions = [];
                foreach ($field as $key => $value) {
                    $conditions[] = $this->_translateCondition($value, isset($condition[$key]) ? $condition[$key] : null);
                }
                $resultCondition = '(' . implode(') ' . \Magento\Framework\DB\Select::SQL_OR . ' (', $conditions) . ')';
            } else {
                $resultCondition = $this->_translateCondition($field, $condition);
            }
            $this->getSelect()
                    ->where('payout_status !=?', 2)
                    ->group('main_table.order_id')
                    ->having($resultCondition, null, Select::TYPE_CONDITION);
            return $this;
        } else {
            return parent::addFieldToFilter($field, $condition);
        }
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
        $this->extendSelectQuery($aliase = 've', $self = 'se', $alease2 = 'vph');
    }

    protected function _renderFiltersBefore() {
        return parent::_renderFiltersBefore();
    }

    public function extendSelectQuery($aliase = 've', $self = 's', $alease2 = 'vph') {

        $this->getSelect()
                ->columns([
					'so_created_at' => 'so.created_at',
                    'total_category_fee_incl_tax' => 'SUM(total_category_fee + total_category_fee_tax)',
                    'total_seller_fee_incl_tax' => 'SUM(total_seller_fee + total_seller_fee_tax)',
                    'total_disbursement_fee_incl_tax' => 'SUM(disbursement_fee + disbursement_fee_tax)',
                    'grand_total_with_shipping' => '(SUM(main_table.base_grand_total + main_table.base_shipping_amount + main_table.base_shipping_tax - main_table.shipping_discount_amount) + so.mcm_base_transaction_fee_incl_tax)',
					'total_with_shipping' => '(SUM(main_table.base_grand_total + main_table.base_shipping_amount + so.base_shipping_tax_amount - so.shipping_discount_amount))',
                    'shipping_total_fee' => '(SUM(main_table.base_shipping_amount))',
                    'mo_total_earning_amount' => '(SUM(total_category_fee + total_category_fee_tax + total_seller_fee + total_seller_fee_tax + disbursement_fee + disbursement_fee_tax) + so.mcm_base_transaction_fee_incl_tax)',
					'payout_status_org' => 'payout_status'
                ])
                ->joinLeft(
                        [$aliase => $this->getTable('omnyfy_vendor_vendor_entity')], "main_table.vendor_id = $aliase.entity_id", [
                    'vendor_name' => "GROUP_CONCAT($aliase.name SEPARATOR ', ')",
                    'vendor_status' => "$aliase.status",
                    'vendor_name_status' => "$aliase.name"
                ])
                ->joinLeft(
                        [$alease2 => $this->getTable('omnyfy_mcm_vendor_payout_history')], "main_table.vendor_id = $alease2.vendor_id AND main_table.id = $alease2.vendor_order_id", [
                    'payout_ref' => "$alease2.payout_ref",
                ])
                ->joinLeft(
                        ['so' => $this->getTable('sales_order')], 'so.entity_id = main_table.order_id', ['mcm_transaction_fee', 'mcm_base_transaction_fee', 'mcm_transaction_fee_surcharge', 'mcm_transaction_fee_tax', 'mcm_base_transaction_fee_incl_tax'])
                ->where('payout_status !=?', 2)
                ->group('main_table.order_id');

        $ObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $context = $ObjectManager->get('Magento\Backend\Model\Session');

        $vendorInfo = $context->getVendorInfo();

        if (!empty($vendorInfo)) {
            $this->getSelect()->where('main_table.vendor_id=?', $vendorInfo['vendor_id']);
        }
        //echo $this->getSelect(); die;
    }

    public function getSelectCountSql() {
        $countSelect = $this->getConnection()->select()
                        ->from(['main_table_new' => $this->getSelect()], [])->columns(new \Zend_Db_Expr('COUNT(*)'));
        return $countSelect;
    }

}
