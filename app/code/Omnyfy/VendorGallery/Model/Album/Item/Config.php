<?php
namespace Omnyfy\VendorGallery\Model\Album\Item;

class Config extends \Magento\Catalog\Model\Product\Media\Config
{
    /**
     * Filesystem directory path of vendor images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseMediaPathAddition()
    {
        return 'vendor/album';
    }

    /**
     * Web-based directory path of product images
     * relatively to media folder
     *
     * @return string
     */
    public function getBaseMediaUrlAddition()
    {
        return 'vendor/album';
    }

    /**
     * @return string
     */
    public function getBaseMediaPath()
    {
        return 'vendor/album';
    }

    /**
     * @return string
     */
    public function getBaseMediaUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . 'vendor/album';
    }
}
