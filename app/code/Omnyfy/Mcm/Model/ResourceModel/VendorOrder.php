<?php

namespace Omnyfy\Mcm\Model\ResourceModel;

class VendorOrder extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

    protected $connection;

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
        $this->_init('omnyfy_mcm_vendor_order', 'id');
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
    
    public function getOrderItems($orderId, $vendorId){
        $adapter = $this->getConnection();
        $table = $this->getTable('omnyfy_mcm_vendor_order_item');
        $select = $adapter->select()->from(
                        $table, ['order_item_id']
                )->where(
                        "vendor_id = ?", (int) $vendorId
                )->where(
                "order_id = ?", (int) $orderId
        );
        return $adapter->fetchAll($select);
    }
}