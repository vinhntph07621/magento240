<?php
namespace Smartwave\Post\Model\ResourceModel\Post;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'id';

    protected function _construct()
    {
        $this->_init('Smartwave\Post\Model\Post', 'Smartwave\Post\Model\ResourceModel\Post');
    }
}
