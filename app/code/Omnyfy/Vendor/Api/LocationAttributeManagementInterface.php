<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-06-07
 * Time: 12:21
 */
namespace Omnyfy\Vendor\Api;

interface LocationAttributeManagementInterface
{
    /**
     * Assign attribute to attribute set
     *
     * @param int $attributeSetId
     * @param int $attributeGroupId
     * @param string $attributeCode
     * @param int $sortOrder
     * @return int
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function assign($attributeSetId, $attributeGroupId, $attributeCode, $sortOrder);

    /**
     * Remove attribute from attribute set
     *
     * @param string $attributeSetId
     * @param string $attributeCode
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     * @return bool
     */
    public function unassign($attributeSetId, $attributeCode);

    /**
     * Retrieve related attributes based on given attribute set ID
     *
     * @param string $attributeSetId
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $attributeSetId is not found
     * @return \Omnyfy\Vendor\Api\Data\LocationAttributeInterface[]
     */
    public function getAttributes($attributeSetId);
}
 