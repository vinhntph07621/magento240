<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-30
 * Time: 11:49
 */
namespace Omnyfy\Vendor\Controller\Adminhtml\Vendor\Attribute;

class Edit extends \Omnyfy\Vendor\Controller\Adminhtml\Vendor\Attribute
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('attribute_id');
        /** @var $model \Omnyfy\Vendor\Model\Resource\Vendor\Eav\Attribute */
        $model = $this->_objectManager->create(
            '\Omnyfy\Vendor\Model\Resource\Vendor\Eav\Attribute'
        )->setEntityTypeId(
            $this->_entityTypeId
        );
        if ($id) {
            $model->load($id);

            if (!$model->getId()) {
                $this->messageManager->addError(__('This attribute no longer exists.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('omnyfy_vendor/*/');
            }

            // entity type check
            if ($model->getEntityTypeId() != $this->_entityTypeId) {
                $this->messageManager->addError(__('This attribute cannot be edited.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('omnyfy_vendor/*/');
            }
        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get('Magento\Backend\Model\Session')->getAttributeData(true);
        if (!empty($data)) {
            $model->addData($data);
        }
        $attributeData = $this->getRequest()->getParam('attribute');
        if (!empty($attributeData) && $id === null) {
            $model->addData($attributeData);
        }

        $this->_coreRegistry->register('entity_attribute', $model);

        $item = $id ? __('Edit Vendor Attribute') : __('New Vendor Attribute');

        $resultPage = $this->createActionPage($item);
        $resultPage->getConfig()->getTitle()->prepend($id ? $model->getName() : __('New Vendor Attribute'));
        $resultPage->getLayout()
            ->getBlock('attribute_edit_js')
            ->setIsPopup((bool)$this->getRequest()->getParam('popup'));
        return $resultPage;
    }
}