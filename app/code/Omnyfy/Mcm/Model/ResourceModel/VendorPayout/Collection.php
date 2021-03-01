<?php

namespace Omnyfy\Mcm\Model\ResourceModel\VendorPayout;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    /**
     * @var string
     */
    protected $_idFieldName = 'payout_id';

    /**
     * Define model & resource model
     */
    protected function _construct() {
        $this->_init(
                'Omnyfy\Mcm\Model\VendorPayout', 'Omnyfy\Mcm\Model\ResourceModel\VendorPayout'
        );
        $this->addFilterToMap('vendor_id', 'main_table.vendor_id');
    }

    /**
     * @return $this|void
     */
    protected function _initSelect() {
        parent::_initSelect();
        $this->getSelect()
                ->joinLeft(
                        ['ve' => $this->getTable('omnyfy_vendor_vendor_entity')], 'main_table.vendor_id = ve.entity_id', [
                    'vendor_name' => 've.name',
                    'vendor_status' => 've.status'
                        ]
        );

        $ObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $context = $ObjectManager->get('Magento\Backend\Model\Session');
        //echo $this->getSelect();

        $vendorInfo = $context->getVendorInfo();

        if (!empty($vendorInfo)) {
            $this->getSelect()->where('vendor_id=' . $vendorInfo['vendor_id']);
        }
    }

}
