<?php
namespace Omnyfy\VendorGallery\Model;

use Magento\Framework\Model\Context;

class Item extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'omnyfy_vendor_gallery_item';
    const IS_THUMBNAIL = 1;
    const IS_NOT_THUMBNAIL = 0;
    const TYPE_VIDEO = 2;
    const TYPE_IMAGE = 1;
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    protected $_cacheTag = 'omnyfy_vendor_gallery_item';

    protected $_eventPrefix = 'omnyfy_vendor_gallery_item';

    /**
     * @var Album\Item\Config
     */
    private $itemConfig;

    protected function _construct()
    {
        $this->_init('Omnyfy\VendorGallery\Model\ResourceModel\Item');
    }

    /**
     * Album constructor.
     * @param Context $context
     * @param \Magento\Framework\Registry $registry
     * @param Album\Item\Config $itemConfig
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $registry,
        \Omnyfy\VendorGallery\Model\Album\Item\Config $itemConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->itemConfig = $itemConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return string
     */
    public function getItemUrl()
    {
        if ($this->getType() == 1) {
            return $this->itemConfig->getBaseMediaUrl() . $this->getData('url');
        }
        return $this->getData('url');
    }

    /**
     * @return string
     */
    public function getPreviewImageUrl() {
        return $this->itemConfig->getBaseMediaUrl() . $this->getData('preview_image');
    }
}
