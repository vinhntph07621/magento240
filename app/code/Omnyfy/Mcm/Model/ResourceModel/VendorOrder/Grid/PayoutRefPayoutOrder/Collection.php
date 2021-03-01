<?php

namespace Omnyfy\Mcm\Model\ResourceModel\VendorOrder\Grid\PayoutRefPayoutOrder;

use Magento\Framework\Api\Search\SearchResultInterface;
use Omnyfy\Mcm\Model\ResourceModel\VendorOrder\Collection as VendorOrderCollection;

class Collection extends VendorOrderCollection implements SearchResultInterface {

    protected function _construct() {
        $this->_init(
                'Magento\Framework\View\Element\UiComponent\DataProvider\Document', 'Omnyfy\Mcm\Model\ResourceModel\VendorOrder'
        );
        $this->addFilterToMap('vendor_name_status', 'name');
        $this->addFilterToMap('vendor_id', 'main_table.vendor_id');
        $this->addFilterToMap('created_at', 'vph.created_at');
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
                    'payout_amount' => 'vph.payout_amount'
                ])
                ->joinLeft(
                        ['vph' => $this->getTable('omnyfy_mcm_vendor_payout_history')], "main_table.vendor_id = vph.vendor_id AND main_table.id = vph.vendor_order_id", [
                    'payout_ref' => "vph.payout_ref",
                    'created_at' => 'vph.created_at'
                ])
                ->joinLeft(
                        ['vp' => $this->getTable('omnyfy_mcm_vendor_payout')], 'vph.payout_id = vp.payout_id', [
                    'ewallet_id' => 'vp.ewallet_id'
                ])
                ->joinLeft(
                        ['ve' => $this->getTable('omnyfy_vendor_vendor_entity')], "main_table.vendor_id = ve.entity_id", [
                    'vendor_name' => 've.name',
                    'vendor_status' => 've.status',
                    'vendor_name_status' => 've.name'
                ])
                ->where('payout_status IN (?)', [1,4])->where('payout_action =?', 1);

        $ObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $context = $ObjectManager->get('Magento\Backend\Model\Session');
        $vendorInfo = $context->getVendorInfo();
        if (!empty($vendorInfo)) {
            $this->getSelect()->where('main_table.vendor_id=?', $vendorInfo['vendor_id']);
        }
        //echo $this->getSelect(); die;
    }

}
