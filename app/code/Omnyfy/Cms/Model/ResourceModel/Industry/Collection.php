<?php
/**
 * Project: CMS Industry M2.
 * User: abhay
 * Date: 01/05/17
 * Time: 2:30 PM
 */
 
namespace Omnyfy\Cms\Model\ResourceModel\Industry;

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
        $this->_init('Omnyfy\Cms\Model\Industry', 'Omnyfy\Cms\Model\ResourceModel\Industry');
    }

}