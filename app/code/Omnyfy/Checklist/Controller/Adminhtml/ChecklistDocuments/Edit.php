<?php

namespace Omnyfy\Checklist\Controller\Adminhtml\ChecklistDocuments;

use Omnyfy\Checklist\Controller\Adminhtml\ChecklistDocuments;

class Edit extends ChecklistDocuments
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

    public function execute()
    {
        $docId = $this->getRequest()->getParam('checklistdocument_id');
        $model = $this->_objectManager->create('Omnyfy\Checklist\Model\ChecklistDocuments');


        if ($docId) {
            $model->load($docId);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This checklist document no longer exists.'));
                $this->_redirect('*/*/');
                return;
            }
        }

        $this->_coreRegistry->register('omnyfy_checklist_checklistdocuments', $model);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy::omnyfy_checklist_checklistdocuments');
        $resultPage->getConfig()->getTitle()->prepend(__('Checklist Documents'));

        return $resultPage;
    }
}