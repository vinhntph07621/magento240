<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Model;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * Rating model
 *
 * @method \Omnyfy\VendorReview\Model\ResourceModel\Rating getResource()
 * @method \Omnyfy\VendorReview\Model\ResourceModel\Rating _getResource()
 * @method array getRatingCodes()
 * @method \Omnyfy\VendorReview\Model\Rating setRatingCodes(array $value)
 * @method array getStores()
 * @method \Omnyfy\VendorReview\Model\Rating setStores(array $value)
 * @method string getRatingCode()
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Rating extends \Magento\Framework\Model\AbstractModel implements IdentityInterface
{
    /**
     * rating entity codes
     */
    const ENTITY_PRODUCT_CODE = 'vendor';

    const ENTITY_PRODUCT_REVIEW_CODE = 'vendor_review';

    const ENTITY_REVIEW_CODE = 'review';

    /**
     * @var \Omnyfy\VendorReview\Model\Rating\OptionFactory
     */
    protected $_ratingOptionFactory;

    /**
     * @var \Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\CollectionFactory
     */
    protected $_ratingCollectionF;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Omnyfy\VendorReview\Model\Rating\OptionFactory $ratingOptionFactory
     * @param \Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\CollectionFactory $ratingCollectionF
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Omnyfy\VendorReview\Model\Rating\OptionFactory $ratingOptionFactory,
        \Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\CollectionFactory $ratingCollectionF,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_ratingOptionFactory = $ratingOptionFactory;
        $this->_ratingCollectionF = $ratingCollectionF;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\VendorReview\Model\ResourceModel\Rating');
    }

    /**
     * @param int $optionId
     * @param int $entityPkValue
     * @return $this
     */
    public function addOptionVote($optionId, $entityPkValue)
    {
        $this->_ratingOptionFactory->create()->setOptionId(
            $optionId
        )->setVendorRatingId(
            $this->getId()
        )->setOmnyfyVendorReviewId(
            $this->getOmnyfyVendorReviewId()
        )->setEntityPkValue(
            $entityPkValue
        )->addVote();
        return $this;
    }

    /**
     * @param int $optionId
     * @return $this
     */
    public function updateOptionVote($optionId)
    {
        $this->_ratingOptionFactory->create()->setOptionId(
            $optionId
        )->setVoteId(
            $this->getVoteId()
        )->setOmnyfyVendorReviewId(
            $this->getOmnyfyVendorReviewId()
        )->setDoUpdate(
            1
        )->addVote();
        return $this;
    }

    /**
     * retrieve rating options
     *
     * @return array
     */
    public function getOptions()
    {
        $options = $this->getData('options');
        if ($options) {
            return $options;
        } elseif ($this->getId()) {
            return $this->_ratingCollectionF->create()->addRatingFilter(
                $this->getId()
            )->setPositionOrder()->load()->getItems();
        }
        return [];
    }

    /**
     * Get rating collection object
     *
     * @param int $entityPkValue
     * @param bool $onlyForCurrentStore
     * @return \Magento\Framework\Data\Collection\AbstractDb
     */
    public function getEntitySummary($entityPkValue, $onlyForCurrentStore = true)
    {
        $this->setEntityPkValue($entityPkValue);
        return $this->_getResource()->getEntitySummary($this, $onlyForCurrentStore);
    }

    /**
     * @param int $reviewId
     * @param bool $onlyForCurrentStore
     * @return array
     */
    public function getReviewSummary($reviewId, $onlyForCurrentStore = true)
    {
        $this->setOmnyfyVendorReviewId($reviewId);
        return $this->_getResource()->getReviewSummary($this, $onlyForCurrentStore);
    }

    /**
     * Get rating entity type id by code
     *
     * @param string $entityCode
     * @return int
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
        // clear cache for all reviews
        return [Review::CACHE_TAG];
    }
}
