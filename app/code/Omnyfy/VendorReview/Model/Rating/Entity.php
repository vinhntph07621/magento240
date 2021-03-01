<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Model\Rating;

/**
 * Ratings entity model
 *
 * @method \Omnyfy\VendorReview\Model\ResourceModel\Rating\Entity _getResource()
 * @method \Omnyfy\VendorReview\Model\ResourceModel\Rating\Entity getResource()
 * @method string getEntityCode()
 * @method \Omnyfy\VendorReview\Model\Rating\Entity setEntityCode(string $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @codeCoverageIgnore
 */
class Entity extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorReview\Model\ResourceModel\Rating\Entity');
    }

    /**
     * @param string $entityCode
     * @return int
     */
    public function getIdByCode($entityCode)
    {
        return $this->_getResource()->getIdByCode($entityCode);
    }
}
