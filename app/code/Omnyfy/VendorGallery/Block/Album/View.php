<?php
namespace Omnyfy\VendorGallery\Block\Album;

use Omnyfy\VendorGallery\Model\ResourceModel\Item\CollectionFactory;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;
    /**
     * @var CollectionFactory
     */
    private $itemCollectionFactory;

    /**
     * @var \Omnyfy\Vendor\Model\Vendor | null
     */
    private $currentVendor = null;

    /**
     * @var \Omnyfy\Vendor\Model\VendorFactory
     */
    private $vendorFactory;

    /**
     * View constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param CollectionFactory $itemCollectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Omnyfy\Vendor\Model\VendorFactory $vendorFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Omnyfy\VendorGallery\Model\ResourceModel\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Framework\Registry $registry,
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory,
        array $data = array()
    ) {
        $this->registry = $registry;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->vendorFactory = $vendorFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        $albumModel = $this->registry->registry('current_vendor_gallery_album');
        $this->pageConfig->getTitle()->set($albumModel->getName());
        return parent::_prepareLayout();
    }

    /**
     * @return \Omnyfy\VendorGallery\Model\ResourceModel\Item\Collection
     */
    public function getAlbumItems()
    {
        $albumModel = $this->registry->registry('current_vendor_gallery_album');
        $itemCollection = $this->itemCollectionFactory->create();
        $itemCollection->getItemsByAlbumId($albumModel->getId());
        return $itemCollection;
    }

    /**
     * @return \Omnyfy\VendorGallery\Model\Album
     */
    public function getAlbumInfo()
    {
        return $this->registry->registry('current_vendor_gallery_album');
    }

    /**
     * @return \Omnyfy\Vendor\Model\Vendor|null
     */
    public function getVendor()
    {
        if ($this->currentVendor === null) {
            $this->currentVendor = $this->vendorFactory->create()->load($this->getAlbumInfo()->getVendorId());
        }

        return $this->currentVendor;
    }
}
