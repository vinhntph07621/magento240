<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Block\Vendor\View;

use Omnyfy\VendorReview\Model\ResourceModel\Review\Collection as ReviewCollection;

/**
 * Detailed Vendor Reviews
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ListView extends \Magento\Framework\View\Element\Template
{
    /**
     * Unused class property
     * @var false
     */
    protected $_forceHasOptions = false;

    /**
     * Review collection
     *
     * @var ReviewCollection
     */
    protected $_reviewsCollection;

    /**
     * Review resource model
     *
     * @var \Omnyfy\VendorReview\Model\ResourceModel\Review\CollectionFactory
     */
    protected $_reviewsColFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    public function __construct(
        \Omnyfy\VendorReview\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        array $data = []
    ) {
        $this->_reviewsColFactory = $collectionFactory;
        $this->_coreRegistry = $coreRegistry;
        $this->_customerFactory = $customerFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get vendor id
     *
     * @return int|null
     */
    public function getVendor()
    {
        $vendor = $this->_coreRegistry->registry('vendor');
        return $vendor;
    }

    /**
     * Prepare vendor review list toolbar
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $toolbar = $this->getLayout()->getBlock('vendor_review_list.toolbar');
        if ($toolbar) {
            $toolbar->setCollection($this->getReviewsCollection());
            $this->setChild('toolbar', $toolbar);
        }

        return $this;
    }

    /**
     * Add rate votes
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->getReviewsCollection()->load()->addRateVotes();
        return parent::_beforeToHtml();
    }

    /**
     * Return review url
     *
     * @param int $id
     * @return string
     */
    public function getReviewUrl($id)
    {
        return $this->getUrl('*/*/view', ['id' => $id]);
    }

    /**
     * Replace review summary html with more detailed review summary
     * Reviews collection count will be jerked here
     *
     * @param \Magento\Catalog\Model\Vendor $vendor
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getReviewsSummaryHtml(
        \Magento\Catalog\Model\Vendor $vendor,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        return $this->getLayout()->createBlock(
                'Omnyfy\VendorReview\Block\Rating\Entity\Detailed'
            )->setEntityId(
                $this->getVendor()->getId()
            )->toHtml() . $this->getLayout()->getBlock(
                'vendor_review_list.count'
            )->assign(
                'count',
                $this->getReviewsCollection()->getSize()
            )->toHtml();
    }

    /**
     * Get collection of reviews
     *
     * @return ReviewCollection
     */
    public function getReviewsCollection()
    {
        if (null === $this->_reviewsCollection) {
            $this->_reviewsCollection = $this->_reviewsColFactory->create()->addStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->addStatusFilter(
                \Omnyfy\VendorReview\Model\Review::STATUS_APPROVED
            )->addEntityFilter(
                'vendor',
                $this->getVendor()->getId()
            )->setDateOrder();
        }
        return $this->_reviewsCollection;
    }

    /**
     * @param $review
     * @return string|null
     */
    public function getCustomerName($review) {
        if (!$review->getCustomerId()) {
            return $review->getNickname();
        }
        $customer = $this->_customerFactory->create()->load($review->getCustomerId());
        if (!$customer->getId()) {
            return null;
        }
        return $customer->getName();
    }
}
