<?php

namespace Omnyfy\Mcm\Model;

use Magento\Framework\Model\AbstractModel;

class VendorPayout extends AbstractModel {

    /**
     * @param \Magento\Framework\Model\Context                        $context
     * @param \Magento\Framework\Registry                             $registry
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb           $resourceCollection
     * @param array                                                   $data
     */
    public function __construct(
    \Magento\Framework\Model\Context $context, \Magento\Framework\Registry $registry, \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null, \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null, array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Define resource model
     */
    protected function _construct() {
        $this->_init('Omnyfy\Mcm\Model\ResourceModel\VendorPayout');
    }
    
    public function getDataByVendorId(){
        if (!$this->hasData('data_by_vendor_id')) {
            $collection = $this->getCollection()->addFieldToFilter('vendor_id', $this->getVendorId());
            $this->setData('data_by_vendor_id', $collection->getFirstItem()->getData());
        }
        return $this->getData('data_by_vendor_id');
    }
    
    public function getVendorName() {
        if (!$this->hasData('vendor_name')) {
            $collection = $this->getCollection();
            $collection->getSelect()->joinLeft(
                    ['ov' => $this->getResource()->getTable('omnyfy_vendor_vendor_entity')], 'main_table.vendor_id = ov.entity_id', ['vendor_name' => 'ov.name']
            )->where('main_table.payout_id = ?', $this->getId());
            $this->setData('vendor_name', $collection->getFirstItem()->getVendorName());
        }
        return $this->getData('vendor_name');
    }
}
