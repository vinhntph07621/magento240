<?php
namespace Omnyfy\VendorGallery\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AlbumLocation extends AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('omnyfy_vendor_gallery_album_location', 'entity_id');
    }
}
