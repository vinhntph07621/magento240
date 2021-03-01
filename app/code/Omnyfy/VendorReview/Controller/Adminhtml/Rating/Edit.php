<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Controller\Adminhtml\Rating;

use Omnyfy\VendorReview\Controller\Adminhtml\Rating as RatingController;
use Magento\Framework\Controller\ResultFactory;

class Edit extends RatingController
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->initEnityId();
        /** @var \Omnyfy\VendorReview\Model\Rating $ratingModel */
        $ratingModel = $this->_objectManager->create('Omnyfy\VendorReview\Model\Rating');
        if ($this->getRequest()->getParam('id')) {
            $ratingModel->load($this->getRequest()->getParam('id'));
        }
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Omnyfy_VendorReview::catalog_reviews_ratings_ratings');
        $resultPage->getConfig()->getTitle()->prepend(__('Ratings'));
        $resultPage->getConfig()->getTitle()->prepend(
            $ratingModel->getId() ? $ratingModel->getRatingCode() : __('New Rating')
        );
        $resultPage->addBreadcrumb(__('Manage Ratings'), __('Manage Ratings'));
        $resultPage->addContent($resultPage->getLayout()->createBlock('Omnyfy\VendorReview\Block\Adminhtml\Rating\Edit'))
            ->addLeft($resultPage->getLayout()->createBlock('Omnyfy\VendorReview\Block\Adminhtml\Rating\Edit\Tabs'));
        return $resultPage;
    }
}
