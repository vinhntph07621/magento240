<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Controller\Adminhtml\Rating;

use Omnyfy\VendorReview\Controller\Adminhtml\Rating as RatingController;
use Magento\Framework\Controller\ResultFactory;

class Index extends RatingController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->initEnityId();
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Omnyfy_VendorReview::vendor_ratings');
        $resultPage->addBreadcrumb(__('Manage Ratings'), __('Manage Ratings'));
        $resultPage->getConfig()->getTitle()->prepend(__('Ratings'));
        return $resultPage;
    }
}
