<?php
/**
 * Review renderer
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Block\Vendor;


class ReviewRenderer extends \Magento\Framework\View\Element\Template
{
    /**
     * Array of available template name
     *
     * @var array
     */
    protected $_availableTemplates = [
        self::FULL_VIEW => 'Omnyfy_VendorReview::helper/summary.phtml',
        self::SHORT_VIEW => 'Omnyfy_VendorReview::helper/summary_short.phtml',
    ];

    /**
     * Review model factory
     *
     * @var \Omnyfy\VendorReview\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Omnyfy\VendorReview\Model\ReviewFactory $reviewFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Omnyfy\VendorReview\Model\ReviewFactory $reviewFactory,
        array $data = []
    ) {
        $this->_reviewFactory = $reviewFactory;
        parent::__construct($context, $data);
    }

    /**
     * Get review summary html
     *
     * @param Vendor $vendor
     * @param string $templateType
     * @param bool $displayIfNoReviews
     *
     * @return string
     */
    public function getReviewsSummaryHtml(
        \Magento\Catalog\Model\Vendor $vendor,
        $templateType = self::DEFAULT_VIEW,
        $displayIfNoReviews = false
    ) {
        if (!$vendor->getRatingSummary() && !$displayIfNoReviews) {
            return '';
        }
        // pick template among available
        if (empty($this->_availableTemplates[$templateType])) {
            $templateType = self::DEFAULT_VIEW;
        }
        $this->setTemplate($this->_availableTemplates[$templateType]);

        $this->setDisplayIfEmpty($displayIfNoReviews);

        if (!$vendor->getRatingSummary()) {
            $this->_reviewFactory->create()->getEntitySummary($vendor, $this->_storeManager->getStore()->getId());
        }
        $this->setVendor($vendor);

        return $this->toHtml();
    }

    /**
     * Get ratings summary
     *
     * @return string
     */
    public function getRatingSummary()
    {
        return $this->getVendor()->getRatingSummary()->getRatingSummary();
    }

    /**
     * Get count of reviews
     *
     * @return int
     */
    public function getReviewsCount()
    {
        return $this->getVendor()->getRatingSummary()->getReviewsCount();
    }

    /**
     * Get review vendor list url
     *
     * @param bool $useDirectLink allows to use direct link for vendor reviews page
     * @return string
     */
    public function getReviewsUrl($useDirectLink = false)
    {
        $vendor = $this->getVendor();
        if ($useDirectLink) {
            return $this->getUrl(
                'vendorreview/vendor/list',
                ['id' => $vendor->getId(), 'category' => $vendor->getCategoryId()]
            );
        }
        return $vendor->getUrlModel()->getUrl($vendor, ['_ignore_category' => true]);
    }
}
