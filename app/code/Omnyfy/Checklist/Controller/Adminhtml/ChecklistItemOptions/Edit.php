<?php


namespace Omnyfy\Checklist\Controller\Adminhtml\ChecklistItemOptions;

class Edit extends \Omnyfy\Checklist\Controller\Adminhtml\ChecklistItemOptions
{

    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('checklistitemoptions_id');
        $model = $this->_objectManager->create('Omnyfy\Checklist\Model\ChecklistItemOptions');
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Checklistitemoptions no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('omnyfy_checklist_checklistitemoptions', $model);
        
        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Checklistitemoptions') : __('New Checklistitemoptions'),
            $id ? __('Edit Checklistitemoptions') : __('New Checklistitemoptions')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Checklistitemoptionss'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getTitle() : __('New Checklistitemoptions'));
        return $resultPage;
    }
}
