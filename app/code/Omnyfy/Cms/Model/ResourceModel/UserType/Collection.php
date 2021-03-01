<?php

namespace Omnyfy\Cms\Model\ResourceModel\UserType;

/**
 * Cms userType collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

     /**
     * @var string
     */
    protected $_idFieldName = 'id';
    
    /**
     * Constructor
     * Configures collection
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Omnyfy\Cms\Model\UserType', 'Omnyfy\Cms\Model\ResourceModel\UserType');
    }

}