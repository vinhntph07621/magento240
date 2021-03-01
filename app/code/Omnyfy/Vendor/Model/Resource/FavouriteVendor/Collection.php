<?php
/**
 * Project: Multi Vendor M2.
 * User: seth
 * Date: 6/9/19
 * Time: 11:43 AM
 */

namespace Omnyfy\Vendor\Model\Resource\FavouriteVendor;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix = 'omnyfy_vendor_favourite_collection';
    protected $_eventObject = 'vendor_favourite';

    protected function _construct()
    {
        $this->_init('Omnyfy\Vendor\Model\FavouriteVendor', 'Omnyfy\Vendor\Model\Resource\FavouriteVendor');
    }
}