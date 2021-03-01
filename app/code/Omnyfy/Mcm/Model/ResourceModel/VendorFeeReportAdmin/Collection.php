<?php

namespace Omnyfy\Mcm\Model\ResourceModel\VendorFeeReportAdmin;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'id';
    /**
     * Define resource model.
     */
    public function _construct()
    {
        $this->_init('Omnyfy\Mcm\Model\VendorFeeReportAdmin',
                'Omnyfy\Mcm\Model\ResourceModel\VendorFeeReportAdmin');
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