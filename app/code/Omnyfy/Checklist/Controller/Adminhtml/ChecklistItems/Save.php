<?php


namespace Omnyfy\Checklist\Controller\Adminhtml\ChecklistItems;

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
            $id = $this->getRequest()->getParam('checklistitems_id');
        
            $model = $this->_objectManager->create('Omnyfy\Checklist\Model\ChecklistItems')->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Checklistitems no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        
            $model->setData($data);
        
            try {
                $model->save();
                $this->messageManager->addSuccessMessage(__('You saved the Checklistitems.'));
                $this->dataPersistor->clear('omnyfy_checklist_checklistitems');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['checklistitems_id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Checklistitems.'));
            }
        
            $this->dataPersistor->set('omnyfy_checklist_checklistitems', $data);
            return $resultRedirect->setPath('*/*/edit', ['checklistitems_id' => $this->getRequest()->getParam('checklistitems_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}
