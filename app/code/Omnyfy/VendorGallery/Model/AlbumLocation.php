<?php
namespace Omnyfy\VendorGallery\Model;

use \Magento\Framework\Model\AbstractModel;

class AlbumLocation extends AbstractModel
{
    /**
     * Initialize resource model
     * @return void
     */
    public function _construct()
    {
        $this->_init('Omnyfy\VendorGallery\Model\ResourceModel\AlbumLocation');
    }
}
