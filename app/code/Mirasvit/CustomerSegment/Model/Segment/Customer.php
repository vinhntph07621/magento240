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



namespace Mirasvit\CustomerSegment\Model\Segment;


use Magento\Framework\Model\AbstractModel;
use Mirasvit\CustomerSegment\Api\Data\Segment\CustomerInterface as SegmentCustomerInterface;

class Customer extends AbstractModel implements SegmentCustomerInterface
{
    /**
     * {@inheritDoc}
     */
    protected function _construct()
    {
        $this->_init('Mirasvit\CustomerSegment\Model\ResourceModel\Segment\Customer');
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritDoc}
     */
    public function getSegmentId()
    {
        return $this->getData(self::SEGMENT_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setSegmentId($segmentId)
    {
        return $this->setData(self::SEGMENT_ID, $segmentId);
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomerId()
    {
        return $this->getData(self::CUSTOMER_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setCustomerId($customerId)
    {
        return $this->setData(self::CUSTOMER_ID, $customerId);
    }

    /**
     * {@inheritDoc}
     */
    public function getBillingAddressId()
    {
        return $this->getData(self::BILLING_ADDRESS_ID);
    }

    /**
     * {@inheritDoc}
     */
    public function setBillingAddressId($id)
    {
        return $this->setData(self::BILLING_ADDRESS_ID, $id);
    }

    /**
     * {@inheritDoc}
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * {@inheritDoc}
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL);
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT);
    }
}