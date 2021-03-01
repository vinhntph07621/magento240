<?php

namespace Omnyfy\Mcm\Model\ResourceModel\VendorPayoutInvoice\VendorPayoutInvoiceOrder;

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
                'Omnyfy\Mcm\Model\VendorPayoutInvoice\VendorPayoutInvoiceOrder', 'Omnyfy\Mcm\Model\ResourceModel\VendorPayoutInvoice\VendorPayoutInvoiceOrder'
        );
        $this->addFilterToMap('vendor_id', 'main_table.vendor_id');
    }

    /**
     * @return $this|void
     */
    protected function _initSelect() {
        parent::_initSelect();

        $ObjectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $context = $ObjectManager->get('Magento\Backend\Model\Session');
        //echo $this->getSelect();

        $vendorInfo = $context->getVendorInfo();

        if (!empty($vendorInfo)) {
            $this->getSelect()->where('vendor_id=' . $vendorInfo['vendor_id']);
        }
    }

}
