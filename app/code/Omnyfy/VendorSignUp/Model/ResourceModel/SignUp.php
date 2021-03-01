<?php

namespace Omnyfy\VendorSignUp\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;

class SignUp extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb {

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
        $this->_init('omnyfy_vendor_signup', 'id');
    }

    public function updateBindsById($binds, $id)
    {
        $conn = $this->getConnection();
        $table = $this->getMainTable();

        $conn->update($table, $binds, ['id=?' => $id]);
    }
}
