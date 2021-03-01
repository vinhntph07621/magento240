<?php


namespace Omnyfy\Checklist\Controller\Adminhtml\ChecklistItemOptions;

class Delete extends \Omnyfy\Checklist\Controller\Adminhtml\ChecklistItemOptions
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('checklistitemoptions_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Omnyfy\Checklist\Model\ChecklistItemOptions');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Checklistitemoptions.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['checklistitemoptions_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Checklistitemoptions to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
