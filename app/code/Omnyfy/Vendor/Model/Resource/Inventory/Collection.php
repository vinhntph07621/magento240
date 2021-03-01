<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 29/1/18
 * Time: 5:35 PM
 */
namespace Omnyfy\Vendor\Model\Resource\Inventory;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'inventory_id';

    protected function _construct()
    {
        $this->_init('Omnyfy\Vendor\Model\Inventory', 'Omnyfy\Vendor\Model\Resource\Inventory');
    }
}