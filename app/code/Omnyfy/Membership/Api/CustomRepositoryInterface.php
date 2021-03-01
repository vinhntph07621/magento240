<?php
namespace Omnyfy\Membership\Api;

/**
 * Customer CRUD interface.
 * @api
 * @since 100.0.2
 */
interface CustomRepositoryInterface {

    /**
     * Get customer by Customer ID.
     *
     * @param int $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If customer with the specified ID does not exist.
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($customerId);


    /**
     *
     * Get Membership Type customer By Customer Id
     *
     * @param int $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getMembershipType($customerId);
}