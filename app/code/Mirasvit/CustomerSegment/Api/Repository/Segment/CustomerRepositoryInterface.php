<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-customer-segment
 * @version   1.0.51
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CustomerSegment\Api\Repository\Segment;

use Mirasvit\CustomerSegment\Api\Data\Segment\CustomerInterface as SegmentCustomerInterface;

interface CustomerRepositoryInterface
{
    /**
     * Retrieve segment customer.
     *
     * @param int $id
     *
     * @return SegmentCustomerInterface|\Magento\Framework\Model\AbstractModel
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * Create or update a segment customer.
     *
     * @param SegmentCustomerInterface $customer
     *
     * @return SegmentCustomerInterface
     */
    public function save(SegmentCustomerInterface $customer);

    /**
     * Delete segment customer.
     *
     * @param SegmentCustomerInterface $customer
     *
     * @return $this
     */
    public function delete(SegmentCustomerInterface $customer);

    /**
     * Delete segment customer by its ID.
     *
     * @param int $customerId
     *
     * @return $this
     */
    public function deleteById($customerId);

    /**
     * Retrieve a segment customers using the specified search criteria.
     *
     * This call returns an array of objects.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Mirasvit\CustomerSegment\Api\Data\Segment\CustomerSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Retrieve collection of segment customers.
     *
     * @return \Mirasvit\CustomerSegment\Model\ResourceModel\Segment\Customer\Collection
     */
    public function getCollection();

    /**
     * Create new segment customer.
     *
     * @return SegmentCustomerInterface
     */
    public function create();
}
