<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Controller\Vendor;

use Omnyfy\VendorReview\Controller\Vendor as VendorController;
use Magento\Framework\Controller\ResultFactory;
use Omnyfy\VendorReview\Model\Review;

class View extends VendorController
{
    /**
     * Load review model with data by passed id.
     * Return false if review was not loaded or review is not approved.
     *
     * @param int $reviewId
     * @return bool|Review
     */
    protected function loadReview($reviewId)
    {
        if (!$reviewId) {
            return false;
        }
        /** @var \Omnyfy\VendorReview\Model\Review $review */
        $review = $this->reviewFactory->create()->load($reviewId);
        if (!$review->getId()
            || !$review->isApproved()
            || !$review->isAvailableOnStore($this->storeManager->getStore())
        ) {
            return false;
        }
        $this->coreRegistry->register('current_review', $review);
        return $review;
    }

    /**
     * Show details of one review
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $review = $this->loadReview((int)$this->getRequest()->getParam('id'));
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
        if (!$review) {
            $resultForward->forward('noroute');
            return $resultForward;
        }

        $vendor = $this->loadVendor($review->getEntityPkValue());
        if (!$vendor) {
            $resultForward->forward('noroute');
            return $resultForward;
        }
        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        return $resultPage;
    }
}
