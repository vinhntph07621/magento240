<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\Vote;

/**
 * Rating votes collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Store list manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\CollectionFactory
     */
    protected $_ratingCollectionF;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\CollectionFactory $ratingCollectionF
     * @param mixed $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\CollectionFactory $ratingCollectionF,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_ratingCollectionF = $ratingCollectionF;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Define model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Omnyfy\VendorReview\Model\Rating\Option\Vote',
            'Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\Vote'
        );
    }

    /**
     * Set review filter
     *
     * @param int $reviewId
     * @return $this
     */
    public function setReviewFilter($reviewId)
    {
        $this->getSelect()->where("main_table.omnyfy_vendor_review_id = ?", $reviewId);
        return $this;
    }

    /**
     * Set EntityPk filter
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityPkFilter($entityId)
    {
        $this->getSelect()->where("entity_pk_value = ?", $entityId);
        return $this;
    }

    /**
     * Set store filter
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreFilter($storeId)
    {
        if ($this->_storeManager->isSingleStoreMode()) {
            return $this;
        }
        $this->getSelect()->join(
            ['rstore' => $this->getTable('omnyfy_vendor_review_store')],
            $this->getConnection()->quoteInto(
                'main_table.omnyfy_vendor_review_id=rstore.omnyfy_vendor_review_id AND rstore.store_id=?',
                (int)$storeId
            ),
            []
        );
        return $this;
    }

    /**
     * Add rating info to select
     *
     * @param int $storeId
     * @return $this
     */
    public function addRatingInfo($storeId = null)
    {
        $connection = $this->getConnection();
        $ratingCodeCond = $connection->getIfNullSql('title.value', 'vendor_rating.vendor_rating_code');
        $this->getSelect()->join(
            ['vendor_rating' => $this->getTable('vendor_rating')],
            'vendor_rating.vendor_rating_id = main_table.vendor_rating_id',
            ['vendor_rating_code']
        )->joinLeft(
            ['title' => $this->getTable('vendor_rating_title')],
            $connection->quoteInto(
                'main_table.vendor_rating_id=title.vendor_rating_id AND title.store_id = ?',
                (int)$this->_storeManager->getStore()->getId()
            ),
            ['vendor_rating_code' => $ratingCodeCond]
        );
        if (!$this->_storeManager->isSingleStoreMode()) {
            if ($storeId == null) {
                $storeId = $this->_storeManager->getStore()->getId();
            }

            if (is_array($storeId)) {
                $condition = $connection->prepareSqlCondition('store.store_id', ['in' => $storeId]);
            } else {
                $condition = $connection->quoteInto('store.store_id = ?', $storeId);
            }

            $this->getSelect()->join(
                ['store' => $this->getTable('vendor_rating_store')],
                'main_table.vendor_rating_id = store.vendor_rating_id AND ' . $condition
            );
        }
        $connection->fetchAll($this->getSelect());
        return $this;
    }

    /**
     * Add option info to select
     *
     * @return $this
     */
    public function addOptionInfo()
    {
        $this->getSelect()->join(
            ['rating_option' => $this->getTable('vendor_rating_option')],
            'main_table.option_id = rating_option.option_id'
        );
        return $this;
    }

    /**
     * Add rating options
     *
     * @return $this
     */
    public function addRatingOptions()
    {
        if (!$this->getSize()) {
            return $this;
        }
        foreach ($this->getItems() as $item) {
            /** @var \Omnyfy\VendorReview\Model\ResourceModel\Rating\Option\Collection $options */
            $options = $this->_ratingCollectionF->create();
            $options->addRatingFilter($item->getVendorRatingId())->load();

            if ($item->getVendorRatingId()) {
                $item->setRatingOptions($options);
            } else {
                return $this;
            }
        }
        return $this;
    }
}
