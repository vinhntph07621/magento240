<?php
namespace Omnyfy\VendorGallery\Model\ResourceModel\Item;

use Omnyfy\VendorGallery\Model\Item;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix = 'omnyfy_vendor_gallery_item_collection';
    protected $_eventObject = 'item_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorGallery\Model\Item', 'Omnyfy\VendorGallery\Model\ResourceModel\Item');
    }

    public function getItemsByAlbumId($albumId)
    {
        $this->addFieldToFilter('album_id', $albumId)
            ->addFieldToFilter('status', Item::STATUS_ACTIVE)
            ->addOrder('position', 'asc');
        return $this;
    }
}
