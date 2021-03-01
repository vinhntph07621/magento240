<?php
namespace Omnyfy\Mcm\Model\ResourceModel\ShippingCalculation;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    public function _construct()
    {
        $this->_init("Omnyfy\Mcm\Model\ShippingCalculation","Omnyfy\Mcm\Model\ResourceModel\ShippingCalculation");
    }
}
?>