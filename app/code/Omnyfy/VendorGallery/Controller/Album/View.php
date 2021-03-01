<?php
namespace Omnyfy\VendorGallery\Controller\Album;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Registry;

/**
 * Class Index
 * @package Omnyfy\VendorGallery\Controller\Album\Index
 */
class View extends Action
{
    /**
     * Index resultPageFactory
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Omnyfy\VendorGallery\Model\AlbumFactory
     */
    private $albumModelFactory;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @var Registry
     */
    private $coreRegistry;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Omnyfy\VendorGallery\Model\AlbumFactory $albumModelFactory
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Omnyfy\VendorGallery\Model\AlbumFactory $albumModelFactory,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        Registry $coreRegistry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->albumModelFactory = $albumModelFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->coreRegistry = $coreRegistry;
        return parent::__construct($context);
    }


    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Forward|\Magento\Framework\Controller\ResultInterface|\Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $albumId = $this->getRequest()->getParam('id');
        if (empty($albumId)) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('noroute');
            return $resultForward;
        }
        $albumModel = $this->albumModelFactory->create()->load($albumId);

        $this->coreRegistry->register('current_vendor_gallery_album', $albumModel);
        return $this->resultPageFactory->create();
    }
}
