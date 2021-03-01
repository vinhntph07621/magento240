<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Controller\Adminhtml\Vendor;

use Omnyfy\VendorReview\Controller\Adminhtml\Vendor as VendorController;
use Magento\Framework\Controller\ResultFactory;

class Delete extends VendorController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $reviewId = $this->getRequest()->getParam('id', false);
        try {
            $this->reviewFactory->create()->setId($reviewId)->aggregate()->delete();

            $this->messageManager->addSuccess(__('The review has been deleted.'));
            if ($this->getRequest()->getParam('ret') == 'pending') {
                $resultRedirect->setPath('vendorreview/*/pending');
            } else {
                $resultRedirect->setPath('vendorreview/*/');
            }
            return $resultRedirect;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong  deleting this review.'));
        }

        return $resultRedirect->setPath('vendorreview/*/edit/', ['id' => $reviewId]);
    }
}
