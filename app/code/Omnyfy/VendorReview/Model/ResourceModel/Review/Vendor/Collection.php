<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Omnyfy\VendorReview\Model\ResourceModel\Review\Vendor;

use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Framework\DB\Select;
use Omnyfy\Vendor\Helper\User;
use Omnyfy\Vendor\Model\Resource\Collection\AbstractCollection;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\State as AppState;
use Magento\Store\Model\Store;

/**
 * Review Vendor Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Collection extends \Omnyfy\Vendor\Model\Resource\Vendor\Collection
{
    /**
     * Entities alias
     *
     * @var array
     */
    protected $_entitiesAlias = [];

    /**
     * Review store table
     *
     * @var string
     */
    protected $_reviewStoreTable;

    /**
     * Add store data flag
     *
     * @var bool
     */
    protected $_addStoreDataFlag = false;

    /**
     * Filter by stores for the collection
     *
     * @var array
     */
    protected $_storesIds = [];

    /**
     * Rating model
     *
     * @var \Omnyfy\VendorReview\Model\RatingFactory
     */
    protected $_ratingFactory;

    /**
     * Rating option vote model
     *
     * @var \Omnyfy\VendorReview\Model\Rating\Option\VoteFactory
     */
    protected $_voteFactory;

    /**
     * Collection constructor.
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param \Magento\Eav\Model\ResourceModel\Helper $resourceHelper
     * @param \Magento\Framework\Validator\UniversalFactory $universalFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param AppState $appState
     * @param User $userHelper
     * @param AdminSession $adminSession
     * @param \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\State $vendorFlatState
     * @param \Omnyfy\Vendor\Helper\Data $helper
     * @param \Magento\Review\Model\RatingFactory $ratingFactory
     * @param \Magento\Review\Model\Rating\Option\VoteFactory $voteFactory
     * @param BackendSession $backendSession
     * @param null $connection
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        AppState $appState,
        User $userHelper,
        AdminSession $adminSession,
        \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\State $vendorFlatState,
        \Omnyfy\Vendor\Helper\Data $helper,
        \Magento\Review\Model\RatingFactory $ratingFactory,
        \Magento\Review\Model\Rating\Option\VoteFactory $voteFactory,
        BackendSession $backendSession,
        $connection = null)
    {
        $this->_ratingFactory = $ratingFactory;
        $this->_voteFactory = $voteFactory;
        $this->backendSession = $backendSession;

        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $storeManager,
            $appState,
            $userHelper,
            $adminSession,
            $vendorFlatState,
            $helper,
            $connection);
    }

    /**
     * Define module
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Omnyfy\Vendor\Model\Vendor', 'Omnyfy\Vendor\Model\Resource\Vendor');
        $this->setRowIdFieldName('omnyfy_vendor_review_id');
        $this->_reviewStoreTable = $this->_resource->getTableName('omnyfy_vendor_review_store');
        $this->_initTables();
    }

    /**
     * Initialize select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->_joinFields();
        return $this;
    }

    /**
     * Adds store filter into array
     *
     * @param int|int[] $storeId
     * @return $this
     */
    public function addStoreFilter($storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStoreId();
        }

        parent::addStoreFilter($storeId);

        if (!is_array($storeId)) {
            $storeId = [$storeId];
        }

        if (!empty($this->_storesIds)) {
            $this->_storesIds = array_intersect($this->_storesIds, $storeId);
        } else {
            $this->_storesIds = $storeId;
        }

        return $this;
    }

    /**
     * Adds specific store id into array
     *
     * @param array $storeId
     * @return $this
     */
    public function setStoreFilter($storeId)
    {
        if (is_array($storeId) && isset($storeId['eq'])) {
            $storeId = array_shift($storeId);
        }

        if (!is_array($storeId)) {
            $storeId = [$storeId];
        }

        if (!empty($this->_storesIds)) {
            $this->_storesIds = array_intersect($this->_storesIds, $storeId);
        } else {
            $this->_storesIds = $storeId;
        }

        return $this;
    }

    /**
     * Applies all store filters in one place to prevent multiple joins in select
     *
     * @param Select $select
     * @return $this
     */
    protected function _applyStoresFilterToSelect(Select $select = null)
    {
        $connection = $this->getConnection();
        $storesIds = $this->_storesIds;
        if (null === $select) {
            $select = $this->getSelect();
        }

        if (is_array($storesIds) && count($storesIds) == 1) {
            $storesIds = array_shift($storesIds);
        }

        if (is_array($storesIds) && !empty($storesIds)) {
            $inCond = $connection->prepareSqlCondition('store.store_id', ['in' => $storesIds]);
            $select->join(
                ['store' => $this->_reviewStoreTable],
                'rt.omnyfy_vendor_review_id=store.omnyfy_vendor_review_id AND ' . $inCond,
                []
            )->group(
                'rt.omnyfy_vendor_review_id'
            );
        } else {
            $select->join(
                ['store' => $this->_reviewStoreTable],
                $connection->quoteInto('rt.omnyfy_vendor_review_id=store.omnyfy_vendor_review_id AND store.store_id = ?', (int)$storesIds),
                []
            );
        }

        return $this;
    }

    /**
     * Add stores data
     *
     * @return $this
     */
    public function addStoreData()
    {
        $this->_addStoreDataFlag = true;
        return $this;
    }

    /**
     * Add customer filter
     *
     * @param int $customerId
     * @return $this
     */
    public function addCustomerFilter($customerId)
    {
        $this->getSelect()->where('rdt.customer_id = ?', $customerId);
        return $this;
    }

    /**
     * Add entity filter
     *
     * @param int $entityId
     * @return $this
     */
    public function addEntityFilter($entityId)
    {
        $this->getSelect()->where('rt.entity_pk_value = ?', $entityId);
        return $this;
    }

    /**
     * Add status filter
     *
     * @param int $status
     * @return $this
     */
    public function addStatusFilter($status)
    {
        $this->getSelect()->where('rt.status_id = ?', $status);
        return $this;
    }

    /**
     * Set date order
     *
     * @param string $dir
     * @return $this
     */
    public function setDateOrder($dir = 'DESC')
    {
        $this->setOrder('rt.created_at', $dir);
        return $this;
    }

    /**
     * Add review summary
     *
     * @return $this
     */
    public function addReviewSummary()
    {
        foreach ($this->getItems() as $item) {
            $model = $this->_ratingFactory->create();
            $model->getReviewSummary($item->getOmnyfyVendorReviewId());
            $item->addData($model->getData());
        }
        return $this;
    }

    /**
     * Add rote votes
     *
     * @return $this
     */
    public function addRateVotes()
    {
        foreach ($this->getItems() as $item) {
            $votesCollection = $this->_voteFactory->create()->getResourceCollection()->setEntityPkFilter(
                $item->getEntityId()
            )->setStoreFilter(
                $this->_storeManager->getStore()->getId()
            )->load();
            $item->setRatingVotes($votesCollection);
        }
        return $this;
    }

    /**
     * Join fields to entity
     *
     * @return $this
     */
    protected function _joinFields()
    {
        $reviewTable = $this->_resource->getTableName('vendor_review');
        $reviewDetailTable = $this->_resource->getTableName('omnyfy_vendor_review_detail');

        $this->addAttributeToSelect('name');

        $this->getSelect()->join(
            ['rt' => $reviewTable],
            'rt.entity_pk_value = e.entity_id',
            ['rt.omnyfy_vendor_review_id', 'review_created_at' => 'rt.created_at', 'rt.entity_pk_value', 'rt.status_id']
        )->join(
            ['rdt' => $reviewDetailTable],
            'rdt.omnyfy_vendor_review_id = rt.omnyfy_vendor_review_id',
            ['rdt.title', 'rdt.nickname', 'rdt.detail', 'rdt.customer_id', 'rdt.store_id']
        );
        return $this;
    }

    /**
     * Retrieve all ids for collection
     *
     * @param null|int|string $limit
     * @param null|int|string $offset
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getAllIds($limit = null, $offset = null)
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Select::ORDER);
        $idsSelect->reset(Select::LIMIT_COUNT);
        $idsSelect->reset(Select::LIMIT_OFFSET);
        $idsSelect->reset(Select::COLUMNS);
        $idsSelect->columns('rt.omnyfy_vendor_review_id');
        return $this->getConnection()->fetchCol($idsSelect);
    }

    /**
     * Get result sorted ids
     *
     * @return array
     */
    public function getResultingIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(Select::LIMIT_COUNT);
        $idsSelect->reset(Select::LIMIT_OFFSET);
        $idsSelect->reset(Select::COLUMNS);
        $idsSelect->reset(Select::ORDER);
        $idsSelect->columns('rt.omnyfy_vendor_review_id');
        return $this->getConnection()->fetchCol($idsSelect);
    }

    /**
     * Render SQL for retrieve vendor count
     *
     * @return string
     */
    public function getSelectCountSql()
    {
        $select = parent::getSelectCountSql();
        $select->reset(Select::COLUMNS)->columns('COUNT(e.entity_id)')->reset(Select::HAVING);

        return $select;
    }

    /**
     * Set order to attribute
     *
     * @param string $attribute
     * @param string $dir
     * @return $this
     */
    public function setOrder($attribute, $dir = 'DESC')
    {
        switch ($attribute) {
            case 'rt.omnyfy_vendor_review_id':
            case 'rt.created_at':
            case 'rt.status_id':
            case 'rdt.title':
            case 'rdt.nickname':
            case 'rdt.detail':
                $this->getSelect()->order($attribute . ' ' . $dir);
                break;
            case 'stores':
// No way to sort
                break;
            case 'type':
                $this->getSelect()->order('rdt.customer_id ' . $dir);
                break;
            default:
                parent::setOrder($attribute, $dir);
                break;
        }
        return $this;
    }

    /**
     * Add attribute to filter
     *
     * @param AbstractAttribute|string $attribute
     * @param array|null $condition
     * @param string $joinType
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function addAttributeToFilter($attribute, $condition = null, $joinType = 'inner')
    {
        switch ($attribute) {
            case 'rt.omnyfy_vendor_review_id':
            case 'rt.created_at':
            case 'rt.status_id':
            case 'rdt.title':
            case 'rdt.nickname':
            case 'rdt.detail':
                $conditionSql = $this->_getConditionSql($attribute, $condition);
                $this->getSelect()->where($conditionSql);
                break;
            case 'stores':
                $this->setStoreFilter($condition);
                break;
            case 'type':
                if ($condition == 1) {
                    $conditionParts = [
                        $this->_getConditionSql('rdt.customer_id', ['is' => new \Zend_Db_Expr('NULL')]),
                        $this->_getConditionSql(
                            'rdt.store_id',
                            ['eq' => \Magento\Store\Model\Store::DEFAULT_STORE_ID]
                        ),
                    ];
                    $conditionSql = implode(' AND ', $conditionParts);
                } elseif ($condition == 2) {
                    $conditionSql = $this->_getConditionSql('rdt.customer_id', ['gt' => 0]);
                } else {
                    $conditionParts = [
                        $this->_getConditionSql('rdt.customer_id', ['is' => new \Zend_Db_Expr('NULL')]),
                        $this->_getConditionSql(
                            'rdt.store_id',
                            ['neq' => \Magento\Store\Model\Store::DEFAULT_STORE_ID]
                        ),
                    ];
                    $conditionSql = implode(' AND ', $conditionParts);
                }
                $this->getSelect()->where($conditionSql);
                break;

            default:
                parent::addAttributeToFilter($attribute, $condition, $joinType);
                break;
        }
        return $this;
    }

    /**
     * Retrieves column values
     *
     * @param string $colName
     * @return array
     */
    public function getColumnValues($colName)
    {
        $col = [];
        foreach ($this->getItems() as $item) {
            $col[] = $item->getData($colName);
        }
        return $col;
    }

    /**
     * Action after load
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        if ($this->_addStoreDataFlag) {
            $this->_addStoreData();
        }
        return $this;
    }

    /**
     * Add store data
     *
     * @return void
     */
    protected function _addStoreData()
    {
        $connection = $this->getConnection();
//$this->_getConditionSql('rdt.customer_id', array('null' => null));
        $reviewsIds = $this->getColumnValues('omnyfy_vendor_review_id');
        $storesToReviews = [];
        if (count($reviewsIds) > 0) {
            $reviewIdCondition = $this->_getConditionSql('omnyfy_vendor_review_id', ['in' => $reviewsIds]);
            $storeIdCondition = $this->_getConditionSql('store_id', ['gt' => 0]);
            $select = $connection->select()->from(
                $this->_reviewStoreTable
            )->where(
                $reviewIdCondition
            )->where(
                $storeIdCondition
            );
            $result = $connection->fetchAll($select);
            foreach ($result as $row) {
                if (!isset($storesToReviews[$row['omnyfy_vendor_review_id']])) {
                    $storesToReviews[$row['omnyfy_vendor_review_id']] = [];
                }
                $storesToReviews[$row['omnyfy_vendor_review_id']][] = $row['store_id'];
            }
        }

        foreach ($this as $item) {
            if (isset($storesToReviews[$item->getOmnyfyVendorReviewId()])) {
                $item->setData('stores', $storesToReviews[$item->getOmnyfyVendorReviewId()]);
            } else {
                $item->setData('stores', []);
            }
        }
    }

    /**
     * Redeclare parent method for store filters applying
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        parent::_beforeLoad();
        $this->_applyStoresFilterToSelect();

        return $this;
    }
}
