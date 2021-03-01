<?php


namespace Omnyfy\VendorFeatured\Model\ResourceModel;

class VendorFeaturedTag extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('omnyfy_vendorfeatured_vendor_featured_tag', 'vendor_featured_tag_id');
    }
}
