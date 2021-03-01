<?php


namespace Omnyfy\VendorAuth\Model\ResourceModel\Log;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Omnyfy\VendorAuth\Model\Log::class,
            \Omnyfy\VendorAuth\Model\ResourceModel\Log::class
        );
    }
}
