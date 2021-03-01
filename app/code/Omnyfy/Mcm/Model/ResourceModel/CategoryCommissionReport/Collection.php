<?php

namespace Omnyfy\Mcm\Model\ResourceModel\CategoryCommissionReport;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define model & resource model
     */
    protected function _construct() {
        $this->_init('Omnyfy\Mcm\Model\CategoryCommissionReport', 'Omnyfy\Mcm\Model\ResourceModel\CategoryCommissionReport');
    }
}
