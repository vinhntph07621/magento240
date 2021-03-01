<?php
namespace Omnyfy\VendorGallery\Model\ResourceModel\AlbumLocation;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Omnyfy\VendorGallery\Model\AlbumLocation',
            'Omnyfy\VendorGallery\Model\ResourceModel\AlbumLocation');
    }

    /**
     * @param $locationId
     * @return array
     */
    public function getAlbumIdByLocationId($locationId)
    {
        $this->addFieldToSelect('album_id')
             ->addFieldToFilter('location_id', $locationId)
             ->getData();
        $albumId = [];
        foreach ($this->getData() as $item) {
            $albumId[] = $item['album_id'];
        }
        return $albumId;
    }
}
