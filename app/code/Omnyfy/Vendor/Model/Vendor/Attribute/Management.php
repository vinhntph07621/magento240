<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-06-07
 * Time: 12:22
 */
namespace Omnyfy\Vendor\Model\Vendor\Attribute;

class Management implements \Omnyfy\Vendor\Api\VendorAttributeManagementInterface
{
    protected $eavAttributeManagement;

    public function __construct(
        \Magento\Eav\Api\AttributeManagementInterface $eavAttributeManagement
    ) {
        $this->eavAttributeManagement = $eavAttributeManagement;
    }

    public function assign($attributeSetId, $attributeGroupId, $attributeCode, $sortOrder)
    {
        return $this->eavAttributeManagement->assign(
            \Omnyfy\Vendor\Api\Data\VendorAttributeInterface::ENTITY_TYPE_CODE,
            $attributeSetId,
            $attributeGroupId,
            $attributeCode,
            $sortOrder
        );
    }

    public function unassign($attributeSetId, $attributeCode)
    {
        return $this->eavAttributeManagement->unassign($attributeSetId, $attributeCode);
    }

    public function getAttributes($attributeSetId)
    {
        return $this->eavAttributeManagement->getAttributes(
            \Omnyfy\Vendor\Api\Data\VendorAttributeInterface::ENTITY_TYPE_CODE,
            $attributeSetId
        );
    }
}
 