<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Model\Review;

/**
 * Review summary
 *
 * @codeCoverageIgnore
 */
class Summary extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Omnyfy\VendorReview\Model\ResourceModel\Review\Summary $resource
     * @param \Omnyfy\VendorReview\Model\ResourceModel\Review\Summary\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Omnyfy\VendorReview\Model\ResourceModel\Review\Summary $resource,
        \Omnyfy\VendorReview\Model\ResourceModel\Review\Summary\Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Get entity primary key value
     *
     * @return int
     */
    public function getEntityPkValue()
    {
        return $this->_getData('entity_pk_value');
    }

    /**
     * Get rating summary data
     *
     * @return string
     */
    public function getRatingSummary()
    {
        return $this->_getData('rating_summary');
    }

    /**
     * Get count of reviews
     *
     * @return int
     */
    public function getReviewsCount()
    {
        return $this->_getData('reviews_count');
    }
}
