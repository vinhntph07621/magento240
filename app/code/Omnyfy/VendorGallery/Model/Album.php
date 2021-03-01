<?php
namespace Omnyfy\VendorGallery\Model;

use Magento\Framework\Model\Context;

class Album extends \Magento\Framework\Model\AbstractModel implements \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'omnyfy_vendor_gallery_album';

    protected $_cacheTag = 'omnyfy_vendor_gallery_album';

    protected $_eventPrefix = 'omnyfy_vendor_gallery_album';

    /**
     * @var Album\Item\Config
     */
    private $itemConfig;

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

    protected function _construct()
    {
        $this->_init('Omnyfy\VendorGallery\Model\ResourceModel\Album');
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Save locations for album
     *
     * @param $locationIds
     */
    public function saveAlbumLocation($locationIds)
    {
        $existedLocationIds = empty($this->getAllLocationIds()) ? [] : $this->getAllLocationIds();

        $resource = $this->getResource();
        $conn = $resource->getConnection();

        if (!empty(array_diff($locationIds, $existedLocationIds))) {
            $insertData = [];
            foreach (array_diff($locationIds, $existedLocationIds) as $locationId) {
                $insertData[] = [
                    'album_id' => $this->getId(),
                    'location_id' => $locationId,
                ];
            }
            $conn->insertMultiple(
                $resource->getTable('omnyfy_vendor_gallery_album_location'),
                $insertData
            );
        }

        if (!empty(array_diff($existedLocationIds, $locationIds))) {
            $deletedData = array_diff($existedLocationIds, $locationIds);
            $query = $conn->deleteFromSelect(
                $conn->select()
                    ->from($resource->getTable('omnyfy_vendor_gallery_album_location'), 'id')
                    ->distinct()
                    ->where('album_id = ?', $this->getId())
                    ->where('location_id in (?)', $deletedData),
                $resource->getTable('omnyfy_vendor_gallery_album_location')
            );
            $conn->query($query);
        }
    }

    /**
     * Get all locations of album
     *
     * @return array
     */
    public function getAllLocationIds()
    {
        $resource = $this->getResource();
        $conn = $resource->getConnection();
        $select = $conn->select()->from(
            ['main_table' => $resource->getTable('omnyfy_vendor_gallery_album_location')],
            'location_id'
        )->where('album_id = ?', $this->getId());
        return $conn->fetchCol($select);
    }

    /**
     * Get url of thumbnail item
     *
     * @return string
     */
    public function getThumbnailUrl()
    {
        return $this->itemConfig->getBaseMediaUrl() . $this->getThumbnailValue();
    }

    /**
     * Get value of thumbnail item
     *
     * @return string
     */
    public function getThumbnailValue()
    {
        $resource = $this->getResource();
        $conn = $resource->getConnection();
        $select = $conn->select()->from(
            ['main_table' => $resource->getTable('omnyfy_vendor_gallery_item')],
            ['url', 'preview_image', 'type']
        )->where('album_id = ?', $this->getId())->where('is_thumbnail', '1');
        $thumbnailInfo = $conn->fetchRow($select);

        if (empty($thumbnailInfo)) {
            $select = $conn->select()->from(
                ['main_table' => $resource->getTable('omnyfy_vendor_gallery_item')],
                ['url', 'preview_image', 'type']
            )->where('album_id = ?', $this->getId())->order('position asc');
            $thumbnailInfo = $conn->fetchRow($select);
        }

        if ($thumbnailInfo['type'] == Item::TYPE_IMAGE) {
            return $thumbnailInfo['url'];
        }
        return $thumbnailInfo['preview_image'];
    }

    /**
     * after delete album. Then delete album's items and album's location too
     * @return \Magento\Framework\Model\AbstractModel|void
     */
    public function afterDelete()
    {
        $resource = $this->getResource();
        $conn = $resource->getConnection();
        $queryDeleteItems = $conn->deleteFromSelect(
            $conn->select()
                ->from($resource->getTable('omnyfy_vendor_gallery_item'), 'album_id')
                ->where('album_id = ?', $this->getId()),
            $resource->getTable('omnyfy_vendor_gallery_item')
        );
        $queryDeleteAlbumLocation = $conn->deleteFromSelect(
            $conn->select()
                ->from($resource->getTable('omnyfy_vendor_gallery_album_location'), 'album_id')
                ->where('album_id = ?', $this->getId()),
            $resource->getTable('omnyfy_vendor_gallery_album_location')
        );
        $conn->query($queryDeleteItems);
        $conn->query($queryDeleteAlbumLocation);
    }
}
