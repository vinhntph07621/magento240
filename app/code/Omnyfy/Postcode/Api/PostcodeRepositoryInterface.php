<?php

namespace Omnyfy\Postcode\Api;

interface PostcodeRepositoryInterface
{

    /**
     * Get postcode by id
     *
     * @param int $postcodeId
     * @param bool $forceReload
     * @return \Omnyfy\Postcode\Api\Data\PostcodeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($postcodeId, $forceReload = false);

    /**
     * Get list
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Omnyfy\Postcode\Api\Data\PostcodeSearchResultInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Get list
     *
     * @param string $keyword
     * @return \Omnyfy\Postcode\Api\Data\PostcodeSimpleParameterSearchInterface
     */
    public function getListByKeyword($keyword);

    /**
     * Get postcode by longitude latitude
     *
     * @param string $lon
     * @param string $lat
     * @return \Omnyfy\Postcode\Api\Data\PostcodeInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getClosest($lon, $lat);

}
