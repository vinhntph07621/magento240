<?php


namespace Omnyfy\Checklist\Controller\Adminhtml\ChecklistItems;

class Delete extends \Omnyfy\Checklist\Controller\Adminhtml\ChecklistItems
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
        $id = $this->getRequest()->getParam('checklistitems_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Omnyfy\Checklist\Model\ChecklistItems');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Checklistitems.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['checklistitems_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Checklistitems to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
