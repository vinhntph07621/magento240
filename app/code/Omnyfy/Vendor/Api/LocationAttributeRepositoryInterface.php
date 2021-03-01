<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/9/17
 * Time: 11:36 AM
 */
namespace Omnyfy\Vendor\Api;

interface LocationAttributeRepositoryInterface extends \Magento\Framework\Api\MetadataServiceInterface
{
    /**
     * Retrieve all attributes for entity type
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\Vendor\Api\Data\LocationAttributeSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve specific attribute
     *
     * @param string $attributeCode
     * @return \Omnyfy\Vendor\Api\Data\LocationAttributeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($attributeCode);
}