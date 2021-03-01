<?php
/**
 * Project: Multi Vendor M2.
 * User: seth
 * Date: 6/9/19
 * Time: 11:27 PM
 */

namespace Omnyfy\Vendor\Model;

use Magento\Framework\Model\AbstractModel;

class FavouriteVendor extends AbstractModel
{
    const CACHE_TAG = 'omnyfy_vendor_customer_favourites';

    protected function _construct()
    {
        $this->_init('Omnyfy\Vendor\Model\Resource\FavouriteVendor');
    }

}