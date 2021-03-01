<?php

namespace Omnyfy\Cms\Model\ResourceModel\Country;

/**
 * Cms Country collection
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
        $this->_init('Omnyfy\Cms\Model\Country', 'Omnyfy\Cms\Model\ResourceModel\Country');
    }

}