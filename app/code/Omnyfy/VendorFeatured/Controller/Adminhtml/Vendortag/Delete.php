<?php


namespace Omnyfy\VendorFeatured\Controller\Adminhtml\Vendortag;

class Delete extends \Omnyfy\VendorFeatured\Controller\Adminhtml\Vendortag
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
        $id = $this->getRequest()->getParam('vendor_tag_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Omnyfy\VendorFeatured\Model\VendorTag');
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Vendor Tag.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['vendor_tag_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Vendor Tag to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}
