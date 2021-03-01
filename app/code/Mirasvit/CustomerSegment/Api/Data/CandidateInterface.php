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



namespace Mirasvit\CustomerSegment\Api\Data;


interface CandidateInterface
{
    /** CANDIDATE BASE DATA */
    const TYPE               = 'type';
    const CUSTOMER_ID        = 'customer_id';
    const ORDER_ID           = 'order_id';
    const STORE_ID           = 'store_id';
    const BILLING_ADDRESS_ID = 'billing_address_id';
    const EMAIL              = 'email';
    const CREATED_AT         = 'created_at';

    /**
     * Object data getter.
     *
     * Return candidate data - various customer's data.
     *
     * If $key is not defined will return all the data as an array.
     * Otherwise it will return value of the element specified by $key.
     * It is possible to use keys like a/b/c for access nested array data
     *
     * If $index is specified it will assume that attribute data is an array
     * and retrieve corresponding member. If data is the string - it will be explode
     * by new line character and converted to array.
     *
     * @param string     $key
     * @param string|int $index
     *
     * @return mixed
     */
    public function getData($key = '', $index = null);

    /**
     * Overwrite data in the object.
     *
     * The $key parameter can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     *
     * If $key is an array, it will overwrite all the data in the object.
     *
     * @param string|array  $key
     * @param mixed         $value
     * @return $this
     */
    public function setData($key, $value = null);

    /**
     * Return customer email.
     *
     * @return string
     */
    public function getEmail();

    /**
     * Set customer email.
     *
     * @param string $email
     *
     * @return $this
     */
    public function setEmail($email);

    /**
     * Get store ID.
     *
     * @return int
     */
    public function getStoreId();

    /**
     * Set customer ID.
     *
     * @return int|null
     */
    public function getCustomerId();

    /**
     * Return customer ID.
     *
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
     * Set order ID.
     *
     * @return int|null
     */
    public function getOrderId();

    /**
     * Return customer ID.
     *
     * @param int $orderId
     *
     * @return $this
     */
    public function setOrderId($orderId);

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
     * Return customer type.
     *
     * @see \Mirasvit\CustomerSegment\Api\Data\SegmentInterface::TYPE_CUSTOMER|GUEST|ALL
     *
     * @return int
     */
    public function getType();
}