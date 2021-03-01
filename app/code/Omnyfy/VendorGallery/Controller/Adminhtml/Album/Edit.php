<?php
namespace Omnyfy\VendorGallery\Controller\Adminhtml\Album;

use Magento\Backend\App\Action;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session.
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Omnyfy_VendorGallery::vendor_gallery_update';

    /**
     * @var \Omnyfy\VendorGallery\Model\AlbumFactory
     */
    protected $albumFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Edit constructor.
     * @param Action\Context $context
     * @param \Omnyfy\VendorGallery\Model\AlbumFactory $albumFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        Action\Context $context,
        \Omnyfy\VendorGallery\Model\AlbumFactory $albumFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->albumFactory = $albumFactory;
        $this->coreRegistry = $coreRegistry;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $albumId = $this->getRequest()->getParam('id');
        /** @var \Omnyfy\VendorGallery\Model\Album $model */
        $model = $this->albumFactory->create();

        if ($albumId) {
            $model->load($albumId);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This news no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        // Restore previously entered form data from session
        $data = $this->_session->getNewsData(true);
        if (!empty($data)) {
            $model->setData($data);
        }
        $this->coreRegistry->register('current_album', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Vendor Album'));

        return $resultPage;
    }
}
