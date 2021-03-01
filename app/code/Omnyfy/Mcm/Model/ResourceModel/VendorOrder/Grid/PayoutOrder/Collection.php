<?php

namespace Omnyfy\Mcm\Model\ResourceModel\VendorOrder\Grid\PayoutOrder;

use Magento\Framework\Api\Search\SearchResultInterface;
use Omnyfy\Mcm\Model\ResourceModel\VendorOrder\Collection as VendorOrderCollection;

class Collection extends VendorOrderCollection implements SearchResultInterface {

    protected function _construct() {
        $this->_init(
                'Magento\Framework\View\Element\UiComponent\DataProvider\Document', 'Omnyfy\Mcm\Model\ResourceModel\VendorOrder'
        );
        $this->addFilterToMap('vendor_id', 'main_table.vendor_id');
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
        $this->getSelect()
                ->columns([
                    'total_category_fee_incl_tax' => '(total_category_fee + total_category_fee_tax)',
                    'total_seller_fee_incl_tax' => '(total_seller_fee + total_seller_fee_tax)',
                    'total_disbursement_fee_incl_tax' => '(disbursement_fee + disbursement_fee_tax)',
                    'grand_total_with_shipping' => '(main_table.base_grand_total + (main_table.base_shipping_amount + base_shipping_tax - main_table.shipping_discount_amount))',
					'total_with_shipping' => '(main_table.base_grand_total + main_table.base_shipping_amount + so.base_shipping_tax_amount - so.shipping_discount_amount)',
                    'payout_amount' => '(main_table.payout_amount)',
                    'payout_shipping' => '(main_table.payout_shipping)',
                ])
				->joinLeft(
                        ['so' => $this->getTable('sales_order')], 'so.entity_id = main_table.order_id')
                ->where('payout_status =?', 0)->where('payout_action =?', 1);
//        echo $this->getSelect();
//        die;
    }

}
