<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-01
 * Time: 17:19
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Location\Attribute;

use Magento\Framework\Exception\NotFoundException;

class Delete extends \Omnyfy\Vendor\Controller\Adminhtml\Location\Attribute
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws NotFoundException
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            throw new NotFoundException(__('Page not found'));
        }

        $id = $this->getRequest()->getParam('attribute_id');
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            $model = $this->_objectManager->create('Omnyfy\Vendor\Model\Resource\Eav\Attribute');

            // entity type check
            $model->load($id);
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                $this->messageManager->addError(__('We can\'t delete the attribute.'));
                return $resultRedirect->setPath('omnyfy_vendor/*/');
            }

            try {
                $model->delete();
                $this->messageManager->addSuccess(__('You deleted the location attribute.'));
                return $resultRedirect->setPath('omnyfy_vendor/*/');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                return $resultRedirect->setPath(
                    'omnyfy_vendor/*/edit',
                    ['attribute_id' => $this->getRequest()->getParam('attribute_id')]
                );
            }
        }
        $this->messageManager->addError(__('We can\'t find an attribute to delete.'));
        return $resultRedirect->setPath('omnyfy_vendor/*/');
    }
}
 