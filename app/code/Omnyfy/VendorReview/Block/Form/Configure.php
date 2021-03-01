<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Block\Form;

/**
 * Review form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Configure extends \Omnyfy\VendorReview\Block\Form
{
    /**
     * Get review vendor id
     *
     * @return int
     */
    public function getVendorId()
    {
        return (int)$this->getRequest()->getParam('vendor_id', false);
    }
}
