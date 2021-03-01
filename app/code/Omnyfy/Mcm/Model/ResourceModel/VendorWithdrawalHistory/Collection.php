<?php

namespace Omnyfy\Mcm\Model\ResourceModel\VendorWithdrawalHistory;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define model & resource model
     */
    protected function _construct() {
        $this->_init(
                'Omnyfy\Mcm\Model\VendorWithdrawalHistory', 'Omnyfy\Mcm\Model\ResourceModel\VendorWithdrawalHistory'
        );
        $this->addFilterToMap('id', 'main_table.id');
    }

    /**
     * @return $this|void
     */
    protected function _initSelect() {
        parent::_initSelect();
        $this->getSelect()->joinLeft(
                ['ba' => $this->getTable('omnyfy_mcm_vendor_bank_account')], 'main_table.bank_account_id = ba.id', [
            'account_name' => 'ba.account_name',
                ]
        )->joinLeft(
                ['ve' => $this->getTable('omnyfy_vendor_vendor_entity')], 'main_table.vendor_id = ve.entity_id', [
            'vendor_name' => 've.name',
            'vendor_name_status' => 've.name',
            'vendor_status' => 've.status'
                ]
        )->joinLeft(
                ['fc' => $this->getTable('omnyfy_mcm_fees_and_charges')], 'main_table.vendor_id = fc.vendor_id', [
            'fees_charges_id' => 'fc.id'
                ]
        );

        $ObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $context = $ObjectManager->get('Magento\Backend\Model\Session');

        $vendorInfo = $context->getVendorInfo();

        if (!empty($vendorInfo)) {
            $this->getSelect()->where('main_table.vendor_id=' . $vendorInfo['vendor_id']);
        }
    }

}
