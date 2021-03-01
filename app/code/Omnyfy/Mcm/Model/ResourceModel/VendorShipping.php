<?php
/**
 * Copyright Â© 2015 Classic. All rights reserved.
 */
namespace Omnyfy\Mcm\Model\ResourceModel;

/**
 * VendorShipping resource
 */
class VendorShipping extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('omnyfy_mcm_vendor_shipping', 'id');
    }

  
}
