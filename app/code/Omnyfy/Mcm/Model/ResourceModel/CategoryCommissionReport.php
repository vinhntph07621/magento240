<?php
/**
 * Copyright Â© 2015 Classic. All rights reserved.
 */
namespace Omnyfy\Mcm\Model\ResourceModel;

/**
 * CategoryCommissionReport resource
 */
class CategoryCommissionReport extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('omnyfy_mcm_category_commission_report', 'id');
    }

  
}
