<?php

namespace Omnyfy\Mcm\Model\ResourceModel\VendorBankAccountType;

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
                'Omnyfy\Mcm\Model\VendorBankAccountType', 'Omnyfy\Mcm\Model\ResourceModel\VendorBankAccountType'
        );
    }
    
    /**
     * @return $this|void
     */
    protected function _initSelect() {
        parent::_initSelect();
    }

}
