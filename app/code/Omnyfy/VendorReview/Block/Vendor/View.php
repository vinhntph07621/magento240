<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Block\Vendor;

use Omnyfy\Vendor\Api\VendorRepositoryInterface;
use Omnyfy\VendorReview\Model\ResourceModel\Review\Collection as ReviewCollection;

/**
 * Vendor Reviews Page
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class View
{
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

    protected $coreRegistry;

    /**
     * @param \Magento\Catalog\Block\Vendor\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Vendor $vendorHelper
     * @param \Magento\Catalog\Model\VendorTypes\ConfigInterface $vendorTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param VendorRepositoryInterface $vendorRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Omnyfy\VendorReview\Model\ResourceModel\Review\CollectionFactory $collectionFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Omnyfy\VendorReview\Model\ResourceModel\Review\CollectionFactory $collectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->_reviewsColFactory = $collectionFactory;

        //@TODO - Any calls to getVendor() make sure it uses the Omnyfy_Vendors to get current vendor
    }

    public function getVendor()
    {
        $this->_coreRegistry->registry('vendor');
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
        \Omnyfy\Vendor\Model\Vendor $vendor,
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
     * Force vendor view page behave like without options
     *
     * @return bool
     */
    public function hasOptions()
    {
        return false;
    }
}
