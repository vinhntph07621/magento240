<?php

namespace Omnyfy\VendorSignUp\Model\ResourceModel\VendorKyc;

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
                'Omnyfy\VendorSignUp\Model\VendorKyc', 'Omnyfy\VendorSignUp\Model\ResourceModel\VendorKyc'
        );
    }
    
    /**
     * @return $this|void
     */
    protected function _initSelect() {
        parent::_initSelect();
//        $this->getSelect()->joinLeft(
//                ['ve' => $this->getTable('omnyfy_vendor_vendor_entity')], 'main_table.vendor_id = ve.entity_id', [
//            'vendor_name' => 've.name',
//            'vendor_status' => 've.status'
//                ]
//        );

        $ObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $context = $ObjectManager->get('Magento\Backend\Model\Session');

        $vendorInfo = $context->getVendorInfo();

        if (!empty($vendorInfo) && $vendorInfo['vendor_id']) {
            $this->getSelect()->where('main_table.vendor_id=' . $vendorInfo['vendor_id']);
        }
    }

}
