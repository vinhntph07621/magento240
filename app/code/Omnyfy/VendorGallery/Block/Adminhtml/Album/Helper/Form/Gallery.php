<?php
namespace Omnyfy\VendorGallery\Block\Adminhtml\Album\Helper\Form;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Catalog\Api\Data\ProductInterface;
use Omnyfy\VendorGallery\Model\Item;

class Gallery extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Gallery
{
    /**
     * Gallery name
     *
     * @var string
     */
    protected $name = 'album[media_gallery]';

    /**
     * @var here you set your ui form
     */
    protected $formName = 'omnyfy_vendor_gallery_edit_tab_images_and_video';

    protected $itemCollectionFactory;

    public function __construct
    (\Magento\Framework\View\Element\Context $context,
     \Magento\Store\Model\StoreManagerInterface $storeManager,
     \Omnyfy\VendorGallery\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory,
     Registry $registry, \Magento\Framework\Data\Form $form,
     $data = []
    ) {
        $this->itemCollectionFactory = $itemCollectionFactory;
        parent::__construct($context, $storeManager, $registry, $form, $data);
    }

    public function getContentHtml()
    {
        $content = $this->getChildBlock('image_content');
        $content->setId($this->getHtmlId() . '_image_content')->setElement($this);
        $content->setFormName($this->formName);
        $galleryJs = $content->getJsObjectName();
        $content->getUploader()->getConfig()->setMegiaGallery($galleryJs);
        return $content->toHtml();
    }
    /**
     * Get product images
     *
     * @return array|null
     */
    public function getImages()
    {
        $images = [];
        $currentAlbum = $this->registry->registry('current_album');
        $itemCollection = $this->itemCollectionFactory->create()->addFieldToFilter('album_id', $currentAlbum->getId());
        foreach ($itemCollection as $item) {
            $itemData = $item->getData();
            if ($itemData['type'] == Item::TYPE_IMAGE) {
                $itemData['media_type'] = 'image';
                $itemData['file'] = $itemData['url'];
            } else {
                $itemData['media_type'] = 'external-video';
                $itemData['video_url'] = $itemData['url'];
                $itemData['file'] = $itemData['url'] = $itemData['preview_image'];
            }
            $itemData['disabled'] = $itemData['status'] == 0 ? 1 : 0;
            $itemData['file'] = $itemData['url'];
            $images[] = $itemData;
        }
        return $images ? $images : null;
    }
}
