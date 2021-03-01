<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Block\Customer;

use Magento\Catalog\Model\Vendor;
use Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\Vote\Collection as VoteCollection;
use Omnyfy\VendorReview\Model\Review;

/**
 * Customer Review detailed view block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * Customer view template name
     *
     * @var string
     */
    protected $_template = 'Omnyfy_VendorReview::customer/view.phtml';

    /**
     * Catalog vendor model
     *
     * @var \Omnyfy\Vendor\Api\VendorRepositoryInterface
     */
    protected $vendorRepository;

    /**
     * Review model
     *
     * @var \Omnyfy\VendorReview\Model\ReviewFactory
     */
    protected $_reviewFactory;

    /**
     * Rating option model
     *
     * @var \Omnyfy\VendorReview\Model\Rating\Option\VoteFactory
     */
    protected $_voteFactory;

    /**
     * Rating model
     *
     * @var \Omnyfy\VendorReview\Model\RatingFactory
     */
    protected $_ratingFactory;

    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var \Omnyfy\Vendor\Helper\Media
     */
    protected $_vendorMedia;

    /**
     * View constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository
     * @param \Omnyfy\VendorReview\Model\ReviewFactory $reviewFactory
     * @param \Omnyfy\VendorReview\Model\Rating\Option\VoteFactory $voteFactory
     * @param \Omnyfy\VendorReview\Model\RatingFactory $ratingFactory
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer
     * @param \Omnyfy\Vendor\Helper\Media $vendorMedia
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository,
        \Omnyfy\VendorReview\Model\ReviewFactory $reviewFactory,
        \Omnyfy\VendorReview\Model\Rating\Option\VoteFactory $voteFactory,
        \Omnyfy\VendorReview\Model\RatingFactory $ratingFactory,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \Omnyfy\Vendor\Helper\Media $vendorMedia,
        array $data = []
    ) {
        $this->vendorRepository = $vendorRepository;
        $this->_reviewFactory = $reviewFactory;
        $this->_voteFactory = $voteFactory;
        $this->_ratingFactory = $ratingFactory;
        $this->currentCustomer = $currentCustomer;
        $this->_vendorMedia = $vendorMedia;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Initialize review id
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setOmnyfyVendorReviewId($this->getRequest()->getParam('id', false));
    }

    /**
     * Get vendor data
     *
     * @return Vendor
     */
    public function getVendorData()
    {
        if ($this->getOmnyfyVendorReviewId() && !$this->getVendorCacheData()) {
            $vendor = $this->vendorRepository->getById($this->getReviewData()->getEntityPkValue());
            $this->setVendorCacheData($vendor);
        }
        return $this->getVendorCacheData();
    }

    /**
     * Get review data
     *
     * @return Review
     */
    public function getReviewData()
    {
        if ($this->getOmnyfyVendorReviewId() && !$this->getReviewCachedData()) {
            $this->setReviewCachedData($this->_reviewFactory->create()->load($this->getOmnyfyVendorReviewId()));
        }
        return $this->getReviewCachedData();
    }

    /**
     * Return review customer url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('vendorreview/customer');
    }

    /**
     * Get review rating collection
     *
     * @return VoteCollection
     */
    public function getRating()
    {
        if (!$this->getRatingCollection()) {
            $ratingCollection = $this->_voteFactory->create()->getResourceCollection()->setReviewFilter(
                $this->getOmnyfyVendorReviewId()
            )->addRatingInfo(
                $this->_storeManager->getStore()->getId()
            )->setStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->load();

            $this->setRatingCollection($ratingCollection->getSize() ? $ratingCollection : false);
        }

        return $this->getRatingCollection();
    }

    /**
     * Get rating summary
     *
     * @return array
     */
    public function getRatingSummary()
    {
        if (!$this->getRatingSummaryCache()) {
            $this->setRatingSummaryCache(
                $this->_ratingFactory->create()->getEntitySummary($this->getVendorData()->getId())
            );
        }
        return $this->getRatingSummaryCache();
    }

    /**
     * Get total reviews
     *
     * @return int
     */
    public function getTotalReviews()
    {
        if (!$this->getTotalReviewsCache()) {
            $this->setTotalReviewsCache(
                $this->_reviewFactory->create()->getTotalReviews($this->getVendorData()->getId()),
                false,
                $this->_storeManager->getStore()->getId()
            );
        }
        return $this->getTotalReviewsCache();
    }

    /**
     * Get formatted date
     *
     * @param string $date
     * @return string
     */
    public function dateFormat($date)
    {
        return $this->formatDate($date, \IntlDateFormatter::LONG);
    }

    /**
     * Get vendor reviews summary
     * 
     * @param \Omnyfy\Vendor\Model\Vendor $vendor
     * @param bool $templateType
     * @param bool $displayIfNoReviews
     * @return mixed
     */
    public function getReviewsSummaryHtml(
        \Omnyfy\Vendor\Model\Vendor $vendor,
        $templateType = false,
        $displayIfNoReviews = false
    ) {
        if (!$vendor->getRatingSummary()) {
            $this->_reviewFactory->create()->getEntitySummary($vendor, $this->_storeManager->getStore()->getId());
        }
        return parent::getReviewsSummaryHtml($vendor, $templateType, $displayIfNoReviews);
    }

    /**
     * @return string
     */
    protected function _toHtml()
    {
        return $this->currentCustomer->getCustomerId() ? parent::_toHtml() : '';
    }

    /**
     * @param $vendorId
     * @return bool|string
     */
    public function getImage($vendorId){
        try {
            $vendor = $this->vendorRepository->getById($vendorId);
            if ($vendor) {
                return $this->_vendorMedia->getVendorLogoUrl($vendor);
            }
        }catch(\Exception $exception){
            $this->_logger->debug($exception->getMessage());
        }
        return "";
    }

    public function getVendorUrl($vendorId)
    {
        return $this->getUrl('shop/brands/view/', ['id' => $vendorId]);
    }

}
