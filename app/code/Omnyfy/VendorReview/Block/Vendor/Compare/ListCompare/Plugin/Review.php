<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Omnyfy\VendorReview\Block\Vendor\Compare\ListCompare\Plugin;

class Review
{
    /**
     * Review model
     *
     * @var \Omnyfy\VendorReview\Model\ReviewFactory
     */
    protected $reviewFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Omnyfy\VendorReview\Model\ReviewFactory $reviewFactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\VendorReview\Model\ReviewFactory $reviewFactory
    ) {
        $this->storeManager = $storeManager;
        $this->reviewFactory = $reviewFactory;
    }

    /**
     * Initialize vendor review
     *
     * @param \Magento\Catalog\Block\Vendor\Compare\ListCompare $subject
     * @param \Magento\Catalog\Model\Vendor $vendor
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeGetReviewsSummaryHtml(
        \Magento\Catalog\Block\Vendor\Compare\ListCompare $subject,
        \Magento\Catalog\Model\Vendor $vendor,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        if (!$vendor->getRatingSummary()) {
            $this->reviewFactory->create()->getEntitySummary($vendor, $this->storeManager->getStore()->getId());
        }
    }
}
