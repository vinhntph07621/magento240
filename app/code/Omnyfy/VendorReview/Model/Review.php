<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Model;

use Omnyfy\Vendor\Model\Vendor;
use Magento\Framework\DataObject\IdentityInterface;
use Omnyfy\VendorReview\Model\ResourceModel\Review\Vendor\Collection as VendorCollection;
use Omnyfy\VendorReview\Model\ResourceModel\Review\Status\Collection as StatusCollection;

/**
 * Review model
 *
 * @method string getCreatedAt()
 * @method \Omnyfy\VendorReview\Model\Review setCreatedAt(string $value)
 * @method \Omnyfy\VendorReview\Model\Review setEntityId(int $value)
 * @method int getEntityPkValue()
 * @method \Omnyfy\VendorReview\Model\Review setEntityPkValue(int $value)
 * @method int getStatusId()
 * @method \Omnyfy\VendorReview\Model\Review setStatusId(int $value)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Review extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    /**
     * Event prefix for observer
     *
     * @var string
     */
    protected $_eventPrefix = 'review';

    /**
     * Cache tag
     */
    const CACHE_TAG = 'review_block';

    /**
     * Vendor entity review code
     */
    const ENTITY_PRODUCT_CODE = 'vendor';

    /**
     * Customer entity review code
     */
    const ENTITY_CUSTOMER_CODE = 'customer';

    /**
     * Category entity review code
     */
    const ENTITY_CATEGORY_CODE = 'category';

    /**
     * Approved review status code
     */
    const STATUS_APPROVED = 1;

    /**
     * Pending review status code
     */
    const STATUS_PENDING = 2;

    /**
     * Not Approved review status code
     */
    const STATUS_NOT_APPROVED = 3;

    /**
     * Review vendor collection factory
     *
     * @var \Omnyfy\VendorReview\Model\ResourceModel\Review\Vendor\CollectionFactory
     */
    protected $vendorCollectionFactory;

    /**
     * Review status collection factory
     *
     * @var \Omnyfy\VendorReview\Model\ResourceModel\Review\Status\CollectionFactory
     */
    protected $_statusFactory;

    /**
     * Review model summary factory
     *
     * @var \Omnyfy\VendorReview\Model\Review\SummaryFactory
     */
    protected $_summaryFactory;

    /**
     * Review model summary factory
     *
     * @var \Omnyfy\VendorReview\Model\Review\SummaryFactory
     */
    protected $_summaryModFactory;

    /**
     * Review model summary
     *
     * @var \Omnyfy\VendorReview\Model\Review\Summary
     */
    protected $_reviewSummary;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Url interface
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlModel;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Omnyfy\VendorReview\Model\ResourceModel\Review\Vendor\CollectionFactory $vendorFactory
     * @param \Omnyfy\VendorReview\Model\ResourceModel\Review\Status\CollectionFactory $statusFactory
     * @param \Omnyfy\VendorReview\Model\ResourceModel\Review\Summary\CollectionFactory $summaryFactory
     * @param \Omnyfy\VendorReview\Model\Review\SummaryFactory $summaryModFactory
     * @param \Omnyfy\VendorReview\Model\Review\Summary $reviewSummary
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $urlModel
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Omnyfy\VendorReview\Model\ResourceModel\Review\Vendor\CollectionFactory $vendorFactory,
        \Omnyfy\VendorReview\Model\ResourceModel\Review\Status\CollectionFactory $statusFactory,
        \Omnyfy\VendorReview\Model\ResourceModel\Review\Summary\CollectionFactory $summaryFactory,
        \Omnyfy\VendorReview\Model\Review\SummaryFactory $summaryModFactory,
        \Omnyfy\VendorReview\Model\Review\Summary $reviewSummary,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlModel,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->vendorCollectionFactory = $vendorFactory;
        $this->_statusFactory = $statusFactory;
        $this->_summaryFactory = $summaryFactory;
        $this->_summaryModFactory = $summaryModFactory;
        $this->_reviewSummary = $reviewSummary;
        $this->_storeManager = $storeManager;
        $this->_urlModel = $urlModel;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorReview\Model\ResourceModel\Review');
    }

    /**
     * Get vendor collection
     *
     * @return VendorCollection
     */
    public function getVendorCollection()
    {
        return $this->vendorCollectionFactory->create();
    }

    /**
     * Get status collection
     *
     * @return StatusCollection
     */
    public function getStatusCollection()
    {
        return $this->_statusFactory->create();
    }

    /**
     * Get total reviews
     *
     * @param int $entityPkValue
     * @param bool $approvedOnly
     * @param int $storeId
     * @return int
     */
    public function getTotalReviews($entityPkValue, $approvedOnly = false, $storeId = 0)
    {
        return $this->getResource()->getTotalReviews($entityPkValue, $approvedOnly, $storeId);
    }

    /**
     * Aggregate reviews
     *
     * @return $this
     */
    public function aggregate()
    {
        $this->getResource()->aggregate($this);
        return $this;
    }

    /**
     * Get entity summary
     *
     * @param Vendor $vendor
     * @param int $storeId
     * @return void
     */
    public function getEntitySummary($vendor, $storeId = 0)
    {
        $summaryData = $this->_summaryModFactory->create()->setStoreId($storeId)->load($vendor->getId());
        $summary = new \Magento\Framework\DataObject();
        $summary->setData($summaryData->getData());
        $vendor->setRatingSummary($summary);
    }

    /**
     * Get pending status
     *
     * @return int
     */
    public function getPendingStatus()
    {
        return self::STATUS_PENDING;
    }

    /**
     * Get review vendor view url
     *
     * @return string
     */
    public function getReviewUrl()
    {
        return $this->_urlModel->getUrl('vendorreview/vendor/view', ['id' => $this->getOmnyfyVendorReviewId()]);
    }

    /**
     * Get vendor view url
     *
     * @param string|int $vendorId
     * @param string|int $storeId
     * @return string
     */
    public function getVendorUrl($vendorId, $storeId)
    {
        if ($storeId) {
            $this->_urlModel->setScope($storeId);
        }

        return $this->_urlModel->getUrl('catalog/vendor/view', ['id' => $vendorId]);
    }

    /**
     * Validate review summary fields
     *
     * @return bool|string[]
     */
    public function validate()
    {
        $errors = [];

        if (!\Zend_Validate::is($this->getTitle(), 'NotEmpty')) {
            $errors[] = __('Please enter a review summary.');
        }

        if (!\Zend_Validate::is($this->getNickname(), 'NotEmpty')) {
            $errors[] = __('Please enter a nickname.');
        }

        if (!\Zend_Validate::is($this->getDetail(), 'NotEmpty')) {
            $errors[] = __('Please enter a review.');
        }

        if (empty($errors)) {
            return true;
        }
        return $errors;
    }

    /**
     * Perform actions after object delete
     *
     * @return \Magento\Framework\Model\AbstractModel
     */
    public function afterDeleteCommit()
    {
        $this->getResource()->afterDeleteCommit($this);
        return parent::afterDeleteCommit();
    }

    /**
     * Append review summary to vendor collection
     *
     * @param VendorCollection $collection
     * @return $this
     */
    public function appendSummary($collection)
    {
        $entityIds = [];
        foreach ($collection->getItems() as $item) {
            $entityIds[] = $item->getEntityId();
        }

        if (sizeof($entityIds) == 0) {
            return $this;
        }

        $summaryData = $this->_summaryFactory->create()
            ->addEntityFilter($entityIds)
            ->addStoreFilter($this->_storeManager->getStore()->getId())
            ->load();

        foreach ($collection->getItems() as $item) {
            foreach ($summaryData as $summary) {
                if ($summary->getEntityPkValue() == $item->getEntityId()) {
                    $item->setRatingSummary($summary);
                }
            }
        }

        return $this;
    }

    /**
     * Check if current review approved or not
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->getStatusId() == self::STATUS_APPROVED;
    }

    /**
     * Check if current review available on passed store
     *
     * @param int|\Magento\Store\Model\Store $store
     * @return bool
     */
    public function isAvailableOnStore($store = null)
    {
        $store = $this->_storeManager->getStore($store);
        if ($store) {
            return in_array($store->getId(), (array) $this->getStores());
        }
        return false;
    }

    /**
     * Get review entity type id by code
     *
     * @param string $entityCode
     * @return int|bool
     */
    public function getEntityIdByCode($entityCode)
    {
        return $this->getResource()->getEntityIdByCode($entityCode);
    }

    /**
     * Return unique ID(s) for each object in system
     *
     * @return array
     */
    public function getIdentities()
    {
        $tags = [];
        if ($this->getEntityPkValue()) {
            $tags[] = Vendor::CACHE_TAG . '_' . $this->getEntityPkValue();
        }
        return $tags;
    }
}
