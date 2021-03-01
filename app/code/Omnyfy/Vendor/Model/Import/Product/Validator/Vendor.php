<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 21/6/18
 * Time: 7:12 PM
 */
namespace Omnyfy\Vendor\Model\Import\Product\Validator;

class Vendor extends \Magento\CatalogImportExport\Model\Import\Product\Validator\AbstractImportValidator
{
    protected $vendorNameToIds;

    protected $vendorIds;

    protected $locationNameToIds;

    protected $locationIds;

    protected $locationIdToVendorIds;

    public function __construct(
        \Omnyfy\Vendor\Model\Resource\Vendor\CollectionFactory $vendorCollectionFactory,
        \Omnyfy\Vendor\Model\Resource\Location\CollectionFactory $locationCollectionFactory
    )
    {
        $collection = $vendorCollectionFactory->create();
        $this->vendorNameToIds = [];
        foreach($collection as $vendor) {
            $this->vendorNameToIds[$vendor->getName()] = $vendor->getId();
        }
        $this->vendorIds = $collection->getAllIds();

        $locationCollection = $locationCollectionFactory->create();
        $$this->locationNameToIds = [];
        foreach($locationCollection as $location) {
            $this->locationNameToIds[$location->getLocationName()] = $location->getId();
            $this->locationIdToVendorIds[$location->getId()] = $location->getVendorId();
        }
        $this->locationIds = $locationCollection->getAllIds();
    }

    public function isValid($value)
    {
        $this->_clearMessages();
        $vendorId = null;
        $vendorName = null;
        $locationId = null;
        $locationName = null;
        if (isset($value['vendor_id']) && strlen($value['vendor_id'])) {
            $vendorId = $value['vendor_id'];
        }
        elseif (isset($value['vendor']) && strlen($value['vendor'])) {
            $vendorName = $value['vendor'];
            if (array_key_exists($vendorName, $this->vendorNameToIds)) {
                $vendorId = $this->vendorNameToIds[$vendorName];
            }
        }

        if (isset($value['location_id']) && strlen($value['location_id'])) {
            $locationId = $value['location_id'];
        }
        elseif (isset($value['location']) && strlen($value['location'])) {
            $locationName = $value['location'];
            if (array_key_exists($locationName, $this->locationNameToIds)) {
                $locationId = $this->locationNameToIds[$locationName];
            }
        }

        if (!empty($vendorId) && !empty($locationId)) {
            if (array_key_exists($locationId, $this->locationIdToVendorIds)
                && $this->locationIdToVendorIds[$locationId] != $vendorId) {
                //return true;
            }
        }
    }
}
