<?php


namespace Omnyfy\VendorFeatured\Model\ResourceModel;

class VendorTag extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('omnyfy_vendorfeatured_vendor_tag', 'vendor_tag_id');
    }
}
