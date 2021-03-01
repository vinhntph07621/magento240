<?php
namespace Omnyfy\VendorGallery\Model\ResourceModel;

class Album extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Album constructor.
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    ) {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('omnyfy_vendor_gallery_album', 'entity_id');
    }
}
