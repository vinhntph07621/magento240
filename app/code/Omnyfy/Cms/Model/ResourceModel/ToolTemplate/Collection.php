<?php

namespace Omnyfy\Cms\Model\ResourceModel\ToolTemplate;

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
        $this->_init('Omnyfy\Cms\Model\ToolTemplate', 'Omnyfy\Cms\Model\ResourceModel\ToolTemplate');
    }

}