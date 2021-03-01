<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-03
 * Time: 15:32
 */
namespace Omnyfy\Vendor\Model\Resource\VendorType;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'type_id';

    protected function _construct()
    {
        $this->_init('Omnyfy\Vendor\Model\VendorType', 'Omnyfy\Vendor\Model\Resource\VendorType');
    }
}
 