<?php

namespace Omnyfy\Mcm\Model\ResourceModel;

class VendorPayoutHistory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string|null $resourcePrefix
     */
    public function __construct(
    \Magento\Framework\Model\ResourceModel\Db\Context $context, \Magento\Framework\Stdlib\DateTime\DateTime $date, \Magento\Framework\Stdlib\DateTime $dateTime, $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->dateTime = $dateTime;
    }

    /**
     * Define main table
     */
    protected function _construct() {
        $this->_init('omnyfy_mcm_vendor_payout_history', 'id');
    }

    /**
     * Process template data before saving
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object) {
        $gmtDate = $this->_date->gmtDate();

        if ($object->isObjectNew() && !$object->getCreatedAt()) {
            $object->setCreatedAt($gmtDate);
        }

        $object->setUpdatedAt($gmtDate);

        return parent::_beforeSave($object);
    }

    /**
     * Process template data before deleting
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _beforeDelete(
    \Magento\Framework\Model\AbstractModel $object
    ) {
        return parent::_beforeDelete($object);
    }

    /**
     * Perform operations after object load
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @return $this
     */
    protected function _afterLoad(\Magento\Framework\Model\AbstractModel $object) {
        return parent::_afterLoad($object);
    }

    public function payoutReceived($vendorId = '') {
        $adapter = $this->getConnection();
        $table = $this->getTable('omnyfy_mcm_vendor_payout_history');
        $select = $adapter->select()->from(
                        $table, ['payout_amount' => 'SUM(payout_amount)', 'status',]
                )->where('status =?', 1);
        if ($vendorId != '') {
            $select = $select->where('vendor_id =?', $vendorId);
        }
        $result = $adapter->fetchRow($select);
        if (!empty($result)) {
            return $result;
        }
        return;
    }

    public function getTotalPayouts($vendorId = '') {
        $adapter = $this->getConnection();
        $table = $this->getTable('omnyfy_mcm_vendor_payout_history');
        $select = $adapter->select()->from(
                        ['vph' => $table], ['payout_amount_vph' => 'SUM(vph.payout_amount)', 'status',]
                )
                ->join(
                        ['vo' => $this->getTable('omnyfy_mcm_vendor_order')], 'vph.vendor_order_id = vo.id', [
                    'total_net_amount' => 'SUM((base_subtotal - base_discount_amount) + (base_shipping_amount - shipping_discount_amount) - (total_category_fee + total_seller_fee + disbursement_fee))',
                    'total_payout_amount_tax' => 'SUM(base_tax_amount - total_tax_onfees)',
                        ]
                )
                ->where('status =?', 1);
        if ($vendorId != '') {
            $select = $select->where('vph.vendor_id =?', $vendorId);
        }
        $result = $adapter->fetchRow($select);
        if (!empty($result)) {
            return $result;
        }
        return;
    }

    public function getLastPayouts($vendorId = '') {
        $adapter = $this->getConnection();
        $table = $this->getTable('omnyfy_mcm_vendor_payout_history');
        $select = $adapter->select()->from(
                        ['vph' => $table], ['payout_amount_vph' => 'SUM(vph.payout_amount)', 'status',]
                )->join(
                        ['vo' => $this->getTable('omnyfy_mcm_vendor_order')], 'vph.vendor_order_id = vo.id', [
                    'total_net_amount' => 'SUM((base_subtotal - base_discount_amount) + (base_shipping_amount - shipping_discount_amount) - (total_category_fee + total_seller_fee + disbursement_fee))',
                    'total_payout_amount_tax' => 'SUM(base_tax_amount - total_tax_onfees)',
                        ]
                )->where('vph.status =?', 1)
                ->where('vph.payout_ref =?', $adapter->select()->from(
                        ['vph1' => $table], ['max_payout_ref' => 'MAX(vph1.payout_ref)']
        ));
        if ($vendorId != '') {
            $select = $select->where('vph.vendor_id =?', $vendorId);
        }
        $result = $adapter->fetchRow($select);
        if (!empty($result)) {
            return $result;
        }
        return;
    }

    public function getWithdrawalAmount($vendorId = '') {
        $adapter = $this->getConnection();
        $table = $this->getTable('omnyfy_mcm_vendor_bank_withdrawals_history');
        $select = $adapter->select()->from(
                $table, ['withdrawal_amount' => 'SUM(withdrawal_amount)']
        );
		// calculate for success withdrawal only
		$select->where('status = ?', 1);
        if ($vendorId != '') {
            $select = $select->where('vendor_id =?', $vendorId);
        }
        $result = $adapter->fetchRow($select);
        if (!empty($result)) {
            return $result;
        }
        return;
    }

    public function getWithdrawalLastUpdated($vendorId = '') {
        $table = $this->getTable('omnyfy_mcm_vendor_bank_withdrawals_history');
        $query = $this->getConnection()->select()->from(
                $table, ['DATE_FORMAT(max(updated_at), "%h:%i %p")']);
        if ($vendorId != '') {
            $query = $query->where('vendor_id =?', $vendorId);
        }
        $result = $this->getConnection()->fetchOne($query);
        if (!empty($result)) {
            return $result;
        }
        return;
    }

    public function ewalletAvailableBalance($vendorId = '') {
        $adapter = $this->getConnection();
        $table = $this->getTable('omnyfy_mcm_vendor_payout');
        $select = $adapter->select()->from(
                $table, ['ewallet_balance' => 'SUM(ewallet_balance)']
        );
        if ($vendorId != '') {
            $select = $select->where('vendor_id =?', $vendorId);
        }
        $result = $adapter->fetchRow($select);
        if (!empty($result)) {
            return $result;
        }
        return;
    }

    public function getPayoutLastUpdated($vendorId = '') {
        $table = $this->getTable('omnyfy_mcm_vendor_payout_history');
        $query = $this->getConnection()->select()->from(
                        $table, ['DATE_FORMAT(max(updated_at), "%h:%i %p")'])
                ->where('status =?', 1);
        if ($vendorId != '') {
            $query = $query->where('vendor_id =?', $vendorId);
        }
        $result = $this->getConnection()->fetchOne($query);
        if (!empty($result)) {
            return $result;
        }
        return;
    }

}
