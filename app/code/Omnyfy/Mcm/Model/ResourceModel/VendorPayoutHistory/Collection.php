<?php

namespace Omnyfy\Mcm\Model\ResourceModel\VendorPayoutHistory;

class Collection extends \Omnyfy\Mcm\Model\ResourceModel\AbstractCollection {
    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define model & resource model
     */
    protected function _construct() {
        $this->_init(
                'Omnyfy\Mcm\Model\VendorPayoutHistory', 'Omnyfy\Mcm\Model\ResourceModel\VendorPayoutHistory'
        );
		$this->addFilterToMap('payout_ref', 'main_table.payout_ref');
		$this->addFilterToMap('vendor_id', 'main_table.vendor_id');
		$this->addFilterToMap('payout_id', 'main_table.payout_id');	
    }

    /**
     * @return $this|void
     */
    protected function _initSelect() {
        parent::_initSelect();
        $this->getSelect()->columns([
            'payout_amount_currency' => 'main_table.payout_amount'
        ])->joinLeft(
                ['vp' => $this->getTable('omnyfy_mcm_vendor_payout')], 'main_table.payout_id = vp.payout_id', [
            'fees_charges_id' => 'vp.fees_charges_id',
            'vendor_id' => 'vp.vendor_id',
            'ewallet_id' => 'vp.ewallet_id'
                ]
        )->joinLeft(
                ['vo' => $this->getTable('omnyfy_mcm_vendor_order')], 'main_table.vendor_order_id = vo.id', [
            'order_increment_id' => 'vo.order_increment_id',
                ]
        )->joinLeft(
                ['vpi' => $this->getTable('omnyfy_mcm_vendor_payout_invoice')], 'main_table.vendor_id = vpi.vendor_id AND main_table.payout_ref = vpi.payout_ref', [
                    'invoice_id' => 'vpi.id',
                    'increment_id' => 'vpi.increment_id',
                ]
        )->joinLeft(
                ['ve' => $this->getTable('omnyfy_vendor_vendor_entity')], 'vp.vendor_id = ve.entity_id', [
            'vendor_name' => 've.name',
            'vendor_status' => 've.status',
            'vendor_name_status' => "ve.name"
                ]
        );
    }
}
