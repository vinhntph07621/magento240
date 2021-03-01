<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Omnyfy\VendorReview\Helper;

use Omnyfy\Vendor\Api\VendorRepositoryInterface;
use Omnyfy\VendorReview\Model\ResourceModel\Review\Collection as ReviewCollection;

/**
 * Default review helper
 */
class Vendor extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_REVIEW_GUETS_ALLOW = 'catalog/review/allow_guest';

    const XML_VENDOR_REVIEW_ENABLED = 'vendorreview/general/enable';

    /**
     * Filter manager
     *
     * @var \Magento\Framework\Filter\FilterManager
     */
    protected $filter;

    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\Filter\FilterManager $filter
     */

    protected $_reviewsColFactory;

    protected $coreRegistry;

    protected $_layout;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Omnyfy\VendorReview\Model\ReviewFactory $reviewFactory,
        \Omnyfy\VendorReview\Model\RatingFactory $ratingFactory
    ) {
        $this->_reviewFactory = $reviewFactory;
        $this->_ratingFactory = $ratingFactory;
        parent::__construct($context);
    }

    public function getReviewSummaryCount($vendor)
    {
        /* This is to add support for Vendor Search as they return a vendor_id */
        if (!empty($vendor->getVendorId())) {
            $entityId = $vendor->getVendorId();
        } else {
            $entityId = $vendor->getId();
        }
        $reviewsCount = $this->_reviewFactory->create()->getTotalReviews($entityId, true);


        $ratingCollection = $this->_reviewFactory->create()->getResourceCollection()->addEntityFilter(
            'vendor', $entityId # TOFIX
        )->addFieldToFilter('status_id', 1)->load();


        return count($ratingCollection);
    }

    public function getStarSummary($vendor)
    {
        /* This is to add support for Vendor Search as they return a vendor_id */
        if (!empty($vendor->getVendorId())) {
            $entityId = $vendor->getVendorId();
        } else {
            $entityId = $vendor->getId();
        }
        $ratingSummary = $this->_ratingFactory->create()->getEntitySummary($entityId);

        if ($ratingSummary) {
            if ($ratingSummary['count'] <= 0) {
                return 0;
            } else {
                return $ratingSummary['sum'] / $ratingSummary['count'];
            }
        } else {
            return 0;
        }
    }

    /**
     * Is Vendor Review enabled in Admin.
     * @return mixed
     */
    public function isVendorReviewEnabled() {
        return $this->scopeConfig->getValue(self::XML_VENDOR_REVIEW_ENABLED);
    }
}
