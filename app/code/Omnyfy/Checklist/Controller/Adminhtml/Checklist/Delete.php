<?php


namespace Omnyfy\Checklist\Controller\Adminhtml\Checklist;

class Delete extends \Omnyfy\Checklist\Controller\Adminhtml\Checklist
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
        $id = $this->getRequest()->getParam('checklist_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Omnyfy\Checklist\Model\Checklist');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Checklist.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['checklist_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Checklist to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
