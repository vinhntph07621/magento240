<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Observer;

use Magento\Framework\Event\ObserverInterface;

class ProcessVendorAfterDeleteEventObserver implements ObserverInterface
{
    /**
     * Review resource model
     *
     * @var \Omnyfy\VendorReview\Model\ResourceModel\Review
     */
    protected $_resourceReview;

    /**
     * @var \Omnyfy\VendorReview\Model\ResourceModel\Rating
     */
    protected $_resourceRating;

    /**
     * @param \Omnyfy\VendorReview\Model\ResourceModel\Review $resourceReview
     * @param \Omnyfy\VendorReview\Model\ResourceModel\Rating $resourceRating
     */
    public function __construct(
        \Omnyfy\VendorReview\Model\ResourceModel\Review $resourceReview,
        \Omnyfy\VendorReview\Model\ResourceModel\Rating $resourceRating
    ) {
        $this->_resourceReview = $resourceReview;
        $this->_resourceRating = $resourceRating;
    }

    /**
     * Cleanup vendor reviews after vendor delete
     *
     * @param   \Magento\Framework\Event\Observer $observer
     * @return  $this
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $eventVendor = $observer->getEvent()->getVendor();
        if ($eventVendor && $eventVendor->getId()) {
            $this->_resourceReview->deleteReviewsByVendorId($eventVendor->getId());
            $this->_resourceRating->deleteAggregatedRatingsByVendorId($eventVendor->getId());
        }

        return $this;
    }
}
