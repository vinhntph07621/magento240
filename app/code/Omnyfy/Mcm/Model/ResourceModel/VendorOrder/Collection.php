<?php

namespace Omnyfy\Mcm\Model\ResourceModel\VendorOrder;

class Collection extends \Omnyfy\Mcm\Model\ResourceModel\AbstractCollection {

    /**
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Define model & resource model
     */
    protected function _construct() {
        $this->_init(
                'Omnyfy\Mcm\Model\VendorOrder', 'Omnyfy\Mcm\Model\ResourceModel\VendorOrder'
        );
    }

    /**
     * @return $this|void
     */
    protected function _initSelect() {
        parent::_initSelect();
    }

}
