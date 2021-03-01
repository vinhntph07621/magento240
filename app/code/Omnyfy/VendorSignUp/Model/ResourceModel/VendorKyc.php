<?php

namespace Omnyfy\VendorSignUp\Model\ResourceModel;

class VendorKyc extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected $_date;

    protected $dateTime;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        $resourcePrefix = null
    ) {
        parent::__construct($context, $resourcePrefix);
        $this->_date = $date;
        $this->dateTime = $dateTime;
    }

    /**
     * Define main table
     */
    protected function _construct() {
        $this->_init('omnyfy_vendor_kyc_details', 'id');
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

    public function updateStatusByVendorId($vendorId, $status) {
        if (empty($vendorId) || empty($status)) {
            return;
        }

        $conn = $this->getConnection();
        $table = $this->getMainTable();
        if (empty($conn) || empty($table)) {
            return;
        }

        $conn->update($table, ['kyc_status' => $status], ['vendor_id=?' => $vendorId]);
    }
}
