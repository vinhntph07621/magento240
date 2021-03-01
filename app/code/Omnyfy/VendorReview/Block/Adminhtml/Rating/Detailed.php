<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Block\Adminhtml\Rating;

use Omnyfy\VendorReview\Model\Rating;
use Omnyfy\VendorReview\Model\Rating\Option;
use Omnyfy\VendorReview\Model\ResourceModel\Rating\Collection as RatingCollection;
use Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\Vote\Collection as VoteCollection;

/**
 * Adminhtml detailed rating stars
 */
class Detailed extends \Magento\Backend\Block\Template
{
    /**
     * Vote collection
     *
     * @var VoteCollection
     */
    protected $_voteCollection = false;

    /**
     * Rating detail template name
     *
     * @var string
     */
    protected $_template = 'Omnyfy_VendorReview::rating/detailed.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Rating resource model
     *
     * @var \Omnyfy\VendorReview\Model\ResourceModel\Rating\CollectionFactory
     */
    protected $_ratingsFactory;

    /**
     * Rating resource option model
     *
     * @var \Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\Vote\CollectionFactory
     */
    protected $_votesFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Omnyfy\VendorReview\Model\ResourceModel\Rating\CollectionFactory $ratingsFactory
     * @param \Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\Vote\CollectionFactory $votesFactory
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Omnyfy\VendorReview\Model\ResourceModel\Rating\CollectionFactory $ratingsFactory,
        \Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\Vote\CollectionFactory $votesFactory,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->_ratingsFactory = $ratingsFactory;
        $this->_votesFactory = $votesFactory;
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
        parent::_construct();

        if ($this->_coreRegistry->registry('review_data')) {
            $this->setOmnyfyVendorReviewId($this->_coreRegistry->registry('review_data')->getOmnyfyVendorReviewId());
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
            if ($this->_coreRegistry->registry('review_data')) {
                $stores = $this->_coreRegistry->registry('review_data')->getStores();

                $stores = array_diff($stores, [0]);

                $ratingCollection = $this->_ratingsFactory->create()->addEntityFilter(
                    'vendor'
                )->setStoreFilter(
                    $stores
                )->setActiveFilter(
                    true
                )->setPositionOrder()->load()->addOptionToItems();

                $this->_voteCollection = $this->_votesFactory->create()->setReviewFilter(
                    $this->getOmnyfyVendorReviewId()
                )->addOptionInfo()->load()->addRatingOptions();
            } elseif (!$this->getIsIndependentMode()) {
                $ratingCollection = $this->_ratingsFactory->create()->addEntityFilter(
                    'vendor'
                )->setStoreFilter(
                    null
                )->setPositionOrder()->load()->addOptionToItems();
            } else {
                $stores = $this->getRequest()->getParam('select_stores') ?: $this->getRequest()->getParam('stores');
                $ratingCollection = $this->_ratingsFactory->create()->addEntityFilter(
                    'vendor'
                )->setStoreFilter(
                    $stores
                )->setPositionOrder()->load()->addOptionToItems();
                if (intval($this->getRequest()->getParam('id'))) {
                    $this->_voteCollection = $this->_votesFactory->create()->setReviewFilter(
                        intval($this->getRequest()->getParam('id'))
                    )->addOptionInfo()->load()->addRatingOptions();
                }
            }
            $this->setRatingCollection($ratingCollection->getSize() ? $ratingCollection : false);
        }
        return $this->getRatingCollection();
    }

    /**
     * Set independent mode
     *
     * @return $this
     */
    public function setIndependentMode()
    {
        $this->setIsIndependentMode(true);
        return $this;
    }

    /**
     * Indicator of whether or not a rating is selected
     *
     * @param Option $option
     * @param \Omnyfy\VendorReview\Model\Rating $rating
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function isSelected($option, $rating)
    {
        if ($this->getIsIndependentMode()) {
            $ratings = $this->getRequest()->getParam('ratings');

            if (isset($ratings[$option->getVendorRatingId()])) {
                return $option->getId() == $ratings[$option->getVendorRatingId()];
            } elseif (!$this->_voteCollection) {
                return false;
            }
        }

        if ($this->_voteCollection) {
            foreach ($this->_voteCollection as $vote) {
                if ($option->getId() == $vote->getOptionId()) {
                    return true;
                }
            }
        }
        return false;
    }
}
