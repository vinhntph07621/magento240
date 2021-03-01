<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Controller\Adminhtml\Rating;

use Omnyfy\VendorReview\Controller\Adminhtml\Rating as RatingController;
use Magento\Framework\Controller\ResultFactory;

class Delete extends RatingController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                /** @var \Omnyfy\VendorReview\Model\Rating $model */
                $model = $this->_objectManager->create('Omnyfy\VendorReview\Model\Rating');
                $model->load($this->getRequest()->getParam('id'))->delete();
                $this->messageManager->addSuccess(__('You deleted the rating.'));
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $resultRedirect->setPath('vendorreview/rating/edit', ['id' => $this->getRequest()->getParam('id')]);
                return $resultRedirect;
            }
        }
        $resultRedirect->setPath('vendorreview/rating/');
        return $resultRedirect;
    }
}
