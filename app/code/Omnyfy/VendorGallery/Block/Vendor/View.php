<?php
namespace Omnyfy\VendorGallery\Block\Vendor;

class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Omnyfy\VendorGallery\Model\ResourceModel\Album\Collection
     */
    private $albumCollection;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Omnyfy\VendorGallery\Model\ResourceModel\Album\Collection $albumCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Omnyfy\VendorGallery\Model\ResourceModel\Album\CollectionFactory $albumCollectionFactory,
        array $data = array()
    ) {
        $this->albumCollection = $albumCollectionFactory->create();
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getVendorId()
    {
        return $this->getRequest()->getParam('id');
    }

    /**
     * @return \Omnyfy\VendorGallery\Model\ResourceModel\Album\Collection
     */
    public function getAlbumCollection()
    {
        $vendorId = $this->getVendorId();
        $albumCollection = $this->albumCollection;

        $albumCollection->addFieldToFilter('main_table.vendor_id', $vendorId)
                        ->addFieldToFilter('status', '1');
        return $albumCollection;
    }
}
