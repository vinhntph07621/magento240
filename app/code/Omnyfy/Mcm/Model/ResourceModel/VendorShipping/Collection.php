<?php

namespace Omnyfy\Mcm\Model\ResourceModel\VendorShipping;

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
                'Omnyfy\Mcm\Model\VendorShipping', 'Omnyfy\Mcm\Model\ResourceModel\VendorShipping'
        );
    }

}
