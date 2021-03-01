<?php

namespace Omnyfy\Mcm\Model\ResourceModel\VendorReportVendor;

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
                'Omnyfy\Mcm\Model\VendorReportVendor', 'Omnyfy\Mcm\Model\ResourceModel\VendorReportVendor'
        );
    }

}
