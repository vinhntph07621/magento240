<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Model\Rating;

/**
 * Rating option model
 *
 * @method \Omnyfy\VendorReview\Model\ResourceModel\Rating\Option _getResource()
 * @method \Omnyfy\VendorReview\Model\ResourceModel\Rating\Option getResource()
 * @method int ->getVendorRatingId()
 * @method \Omnyfy\VendorReview\Model\Rating\Option setRatingId(int $value)
 * @method string getCode()
 * @method \Omnyfy\VendorReview\Model\Rating\Option setCode(string $value)
 * @method int getValue()
 * @method \Omnyfy\VendorReview\Model\Rating\Option setValue(int $value)
 * @method int getPosition()
 * @method \Omnyfy\VendorReview\Model\Rating\Option setPosition(int $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @codeCoverageIgnore
 */
class Option extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorReview\Model\ResourceModel\Rating\Option');
    }

    /**
     * @return $this
     */
    public function addVote()
    {
        $this->getResource()->addVote($this);
        return $this;
    }

    /**
     * @param mixed $id
     * @return $this
     */
    public function setId($id)
    {
        $this->setOptionId($id);
        return $this;
    }
}
