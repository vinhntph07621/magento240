<?php


namespace Omnyfy\VendorSearch\Model\ResourceModel\SearchHistory;

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
            'Omnyfy\VendorSearch\Model\SearchHistory',
            'Omnyfy\VendorSearch\Model\ResourceModel\SearchHistory'
        );
    }
}
