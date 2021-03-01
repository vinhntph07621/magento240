<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Block\Adminhtml\Rating;

use Omnyfy\VendorReview\Model\ResourceModel\Rating\Collection as RatingCollection;

/**
 * Adminhtml summary rating stars
 */
class Summary extends \Magento\Backend\Block\Template
{
    /**
     * Rating summary template name
     *
     * @var string
     */
    protected $_template = 'Omnyfy_VendorReview::rating/stars/summary.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Rating resource option model
     *
     * @var \Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\Vote\CollectionFactory
     */
    protected $_votesFactory;

    /**
     * Rating model
     *
     * @var \Omnyfy\VendorReview\Model\RatingFactory
     */
    protected $_ratingFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\Vote\CollectionFactory $votesFactory
     * @param \Omnyfy\VendorReview\Model\RatingFactory $ratingFactory
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\Vote\CollectionFactory $votesFactory,
        \Omnyfy\VendorReview\Model\RatingFactory $ratingFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_votesFactory = $votesFactory;
        $this->_ratingFactory = $ratingFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize review data
     *
     * @return void
     */
    protected function _construct()
    {
        if ($this->_coreRegistry->registry('review_data')) {
            $this->setOmnyfyVendorReviewId($this->_coreRegistry->registry('review_data')->getId());
        }
    }

    /**
     * Get collection of ratings
     *
     * @return RatingCollection
     */
    public function getRating()
    {
        if (!$this->getRatingCollection()) {
            $ratingCollection = $this->_votesFactory->create()->setReviewFilter(
                $this->getOmnyfyVendorReviewId()
            )->addRatingInfo()->load();
            $this->setRatingCollection($ratingCollection->getSize() ? $ratingCollection : false);
        }
        return $this->getRatingCollection();
    }

    /**
     * Get rating summary
     *
     * @return string
     */
    public function getRatingSummary()
    {
        if (!$this->getRatingSummaryCache()) {
            $this->setRatingSummaryCache($this->_ratingFactory->create()->getReviewSummary($this->getOmnyfyVendorReviewId()));
        }

        return $this->getRatingSummaryCache();
    }
}
