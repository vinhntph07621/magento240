<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-03
 * Time: 15:28
 */
namespace Omnyfy\Vendor\Model\Resource;

class VendorType extends \Omnyfy\Core\Model\ResourceModel\AbstractDbModel
{
    protected function _construct()
    {
        $this->_init('omnyfy_vendor_vendor_type', 'type_id');
    }

    protected function getUpdateFields()
    {
        return [
            'search_by',
            'view_mode',
            'vendor_attribute_set_id',
            'location_attribute_set_id',
            'status'
        ];
    }

    public function updateAttributeSetId($typeId, $vendorAttributeSetId, $locationAttributeSetId)
    {
        if (empty($typeId) || empty($vendorAttributeSetId) || empty($locationAttributeSetId)) {
            return;
        }

        $conn = $this->getConnection();

        $vendorTable = $this->getTable('omnyfy_vendor_vendor_entity');
        $conn->update(
            $vendorTable,
            ['attribute_set_id' => $vendorAttributeSetId],
            ['type_id=?' => $typeId]
        );

        $locationTable = $this->getTable('omnyfy_vendor_location_entity');
        $conn->update(
            $locationTable,
            ['attribute_set_id' => $locationAttributeSetId],
            ['vendor_type_id=?' => $typeId]
        );
    }
}
 