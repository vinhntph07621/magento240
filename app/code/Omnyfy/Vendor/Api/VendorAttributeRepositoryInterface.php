<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 11/9/17
 * Time: 12:10 PM
 */
namespace Omnyfy\Vendor\Api;

interface VendorAttributeRepositoryInterface extends \Magento\Framework\Api\MetadataServiceInterface
{
    /**
     * Retrieve all attributes for entity type
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\Vendor\Api\Data\VendorAttributeSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve specific attribute
     *
     * @param string $attributeCode
     * @return \Omnyfy\Vendor\Api\Data\VendorAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($attributeCode);

    /**
     * Save attribute data
     *
     * @param \Omnyfy\Vendor\Api\Data\VendorAttributeInterface $attribute
     * @return \Omnyfy\Vendor\Api\Data\VendorAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\StateException
     */
    public function save(\Omnyfy\Vendor\Api\Data\VendorAttributeInterface $attribute);

    /**
     * Delete Attribute
     *
     * @param \Omnyfy\Vendor\Api\Data\VendorAttributeInterface $attribute
     * @return bool True if the entity was deleted (always true)
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function delete(\Omnyfy\Vendor\Api\Data\VendorAttributeInterface $attribute);

    /**
     * Delete Attribute by id
     *
     * @param string $attributeCode
     * @return bool
     * @throws \Magento\Framework\Exception\StateException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function deleteById($attributeCode);
}