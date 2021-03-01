<?php


namespace Omnyfy\VendorFeatured\Controller\Adminhtml\Vendortag;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('vendor_tag_id');
        
            $model = $this->_objectManager->create('Omnyfy\VendorFeatured\Model\VendorTag')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Vendor Tag no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        
            $model->setData($data);
        
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Vendor Tag.'));
                $this->dataPersistor->clear('omnyfy_vendorfeatured_vendor_tag');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['vendor_tag_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Vendor Tag.'));
            }
        
            $this->dataPersistor->set('omnyfy_vendorfeatured_vendor_tag', $data);
            return $resultRedirect->setPath('*/*/edit', ['vendor_tag_id' => $this->getRequest()->getParam('vendor_tag_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
