<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-03
 * Time: 13:37
 */

namespace Omnyfy\Vendor\Model;

use \Omnyfy\Vendor\Api\Data\VendorTypeInterface;

class VendorType extends \Magento\Framework\Model\AbstractModel implements VendorTypeInterface
{
    const CACHE_TAG = 'omnyfy_vendor_type';

    protected $_eventPrefix = 'omnyfy_vendor_type';

    protected function _construct()
    {
        $this->_init('Omnyfy\Vendor\Model\Resource\VendorType');
    }

    public function getTypeName()
    {
        return $this->getData(self::TYPE_NAME);
    }

    public function setTypeName($name)
    {
        return $this->setData(self::TYPE_NAME, $name);
    }

    public function getSearchBy()
    {
        return $this->getData(self::SEARCH_BY);
    }

    public function setSearchBy($searchBy)
    {
        return $this->setData(self::SEARCH_BY, $searchBy);
    }

    public function getViewMode()
    {
        return $this->getData(self::VIEW_MODE);
    }

    public function setViewMode($viewMode)
    {
        return $this->setData(self::VIEW_MODE, $viewMode);
    }

    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    public function getVendorAttributeSetId()
    {
        return $this->getData(self::VENDOR_ATTR_SET_ID);
    }

    public function setVendorAttributeSetId($vendorAttributeSetId)
    {
        return $this->setData(self::VENDOR_ATTR_SET_ID, $vendorAttributeSetId);
    }

    public function getLocationAttributeSetId()
    {
        return $this->getData(self::LOCATION_ATTR_SET_ID);
    }

    public function setLocationAttributeSetId($locationAttributeSetId)
    {
        return $this->setData(self::LOCATION_ATTR_SET_ID, $locationAttributeSetId);
    }
}