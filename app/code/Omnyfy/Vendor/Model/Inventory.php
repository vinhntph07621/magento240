<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 29/1/18
 * Time: 5:26 PM
 */
namespace Omnyfy\Vendor\Model;

class Inventory extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'omnyfy_vendor_inventory';

    protected function _construct()
    {
        $this->_init('Omnyfy\Vendor\Model\Resource\Inventory');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}