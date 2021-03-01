<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Controller\Adminhtml\Vendor;

use Omnyfy\VendorReview\Controller\Adminhtml\Vendor as VendorController;
use Magento\Framework\Controller\ResultFactory;

class Index extends VendorController
{
    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if ($this->getRequest()->getParam('ajax')) {
            /** @var \Magento\Backend\Model\View\Result\Forward $resultForward */
            $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            $resultForward->forward('reviewGrid');
            return $resultForward;
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Omnyfy_VendorReview::vendor_reviews_all');
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Reviews'));
        $resultPage->getConfig()->getTitle()->prepend(__('Reviews'));
        $resultPage->addContent($resultPage->getLayout()->createBlock('Omnyfy\VendorReview\Block\Adminhtml\Main'));
        return $resultPage;
    }
}
