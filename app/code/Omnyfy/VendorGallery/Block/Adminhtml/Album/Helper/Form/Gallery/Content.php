<?php
namespace Omnyfy\VendorGallery\Block\Adminhtml\Album\Helper\Form\Gallery;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;

class Content extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery\Content
{
    protected $_template = 'Omnyfy_VendorGallery::album/helper/gallery.phtml';

    /**
     * @var \Omnyfy\VendorGallery\Model\Album\Item\Config
     */
    protected $albumConfig;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    private $imageHelper;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Catalog\Model\Product\Media\Config $mediaConfig,
        \Omnyfy\VendorGallery\Model\Album\Item\Config $albumConfig,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->albumConfig = $albumConfig;
        $this->registry = $registry;
        parent::__construct($context, $jsonEncoder, $mediaConfig, $data);
    }

    protected function _prepareLayout()
    {
        $this->addChild('uploader', 'Magento\Backend\Block\Media\Uploader');

        $this->getUploader()->getConfig()->setUrl(
            $this->_urlBuilder->addSessionParam()->getUrl('vendor_gallery/album/upload')
        )->setFileField(
            'image'
        )->setFilters(
            [
                'images' => [
                    'label' => __('Images (.gif, .jpg, .png)'),
                    'files' => ['*.gif', '*.jpg', '*.jpeg', '*.png'],
                ],
            ]
        );
        $this->_eventManager->dispatch('vendor_gallery_prepare_layout', ['block' => $this]);
        return $this;
    }

    public function getImageTypes()
    {
        $currentAlbum = $this->registry->registry('current_album');
        $imageType = ['thumbnail' => [
            'code' => 'thumbnail',
            'value' => $currentAlbum->getThumbnailValue(),
            'label' => 'Thumbnail',
            'scope' => 'STORE VIEW',
            'name' => 'album[thumbnail]'
        ]];
        return $imageType;
    }

    public function getMediaAttributes()
    {
        return [];
    }

    public function getImagesJson()
    {
        $value = $this->getElement()->getImages();
        if (is_array($value) &&
            count($value)
        ) {
            $mediaDir = $this->_filesystem->getDirectoryRead(DirectoryList::MEDIA);
            $images = $this->sortImagesByPosition($value);
            foreach ($images as &$image) {
                $image['url'] = $this->albumConfig->getMediaUrl($image['url']);
                try {
                    $fileHandler = $mediaDir->stat($this->albumConfig->getMediaPath($image['file']));
                    $image['size'] = $fileHandler['size'];
                } catch (FileSystemException $e) {
                    $image['url'] = $this->getImageHelper()->getDefaultPlaceholderUrl('small_image');
                    $image['size'] = 0;
                    $this->_logger->warning($e);
                }
            }
            return $this->_jsonEncoder->encode($images);
        }
        return '[]';
    }

    /**
     * @return \Magento\Catalog\Helper\Image
     * @deprecated
     */
    private function getImageHelper()
    {
        if ($this->imageHelper === null) {
            $this->imageHelper = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Catalog\Helper\Image');
        }
        return $this->imageHelper;
    }

    /**
     * Sort images array by position key
     *
     * @param array $images
     * @return array
     */
    private function sortImagesByPosition($images)
    {
        if (is_array($images)) {
            usort($images, function ($imageA, $imageB) {
                return ($imageA['position'] < $imageB['position']) ? -1 : 1;
            });
        }
        return $images;
    }
}
