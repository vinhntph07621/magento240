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



namespace Mirasvit\CustomerSegment\Api\Data\Segment;


interface CustomerInterface
{
    const TABLE_NAME = 'mst_customersegment_segment_customer';

    /** Entity Fields */
    const ID                 = 'segment_customer_id';
    const SEGMENT_ID         = 'segment_id';
    const CUSTOMER_ID        = 'customer_id';
    const BILLING_ADDRESS_ID = 'billing_address_id';
    const EMAIL              = 'email';
    const CREATED_AT         = 'created_at';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getSegmentId();

    /**
     * @param int $segmentId
     *
     * @return $this
     */
    public function setSegmentId($segmentId);

    /**
     * @return int|null
     */
    public function getCustomerId();

    /**
     * @param int $customerId
     *
     * @return $this
     */
    public function setCustomerId($customerId);

    /**
     * @return int|null
     */
    public function getBillingAddressId();

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setBillingAddressId($id);

    /**
     * @return string
     */
    public function getEmail();

    /**
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Delete segment customer from database.
     *
     * @return $this
     * @throws \Exception
     * @deprecated
     */
    public function delete();

    /**
     * Save segment customer data.
     *
     * @return $this
     * @throws \Exception
     * @deprecated
     */
    public function save();

    /**
     * Load segment customer data.
     *
     * @param integer $modelId
     * @param null|string $field
     *
     * @return $this
     * @deprecated
     */
    public function load($modelId, $field = null);
}