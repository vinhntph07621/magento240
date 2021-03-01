<?php


namespace Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag;

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
            'Omnyfy\VendorFeatured\Model\VendorFeaturedTag',
            'Omnyfy\VendorFeatured\Model\ResourceModel\VendorFeaturedTag'
        );
    }
}
