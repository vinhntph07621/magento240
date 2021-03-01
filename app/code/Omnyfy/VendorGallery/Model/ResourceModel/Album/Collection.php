<?php
namespace Omnyfy\VendorGallery\Model\ResourceModel\Album;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'omnyfy_vendor_gallery_album_collection';
    protected $_eventObject = 'album_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorGallery\Model\Album', 'Omnyfy\VendorGallery\Model\ResourceModel\Album');
    }
}
