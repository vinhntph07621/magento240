<?php


namespace Omnyfy\VendorFeatured\Model\ResourceModel\VendorTag;

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
            'Omnyfy\VendorFeatured\Model\VendorTag',
            'Omnyfy\VendorFeatured\Model\ResourceModel\VendorTag'
        );
    }
}
