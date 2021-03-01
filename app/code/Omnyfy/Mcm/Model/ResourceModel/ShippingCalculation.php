<?php
namespace Omnyfy\Mcm\Model\ResourceModel;

/**
 * Class ShippingCalculation
 * @package Omnyfy\Mcm\Model\ResourceModel
 */
class ShippingCalculation extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     *
     */
    public function _construct()
    {
        $this->_init("omnyfy_mcm_shipping_calculation","id");
    }
}
?>