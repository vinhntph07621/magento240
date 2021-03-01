<?php

namespace Omnyfy\VendorSignUp\Model\ResourceModel\SignUp;

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
                'Omnyfy\VendorSignUp\Model\SignUp', 'Omnyfy\VendorSignUp\Model\ResourceModel\SignUp'
        );
    }
    
    /**
     * @return $this|void
     */
    protected function _initSelect() {
        parent::_initSelect();
		$this->getSelect()->where("main_table.created_by = 'Customer'");
    }
}
