<?php
namespace Omnyfy\Vendor\Model\Resource\Location;

use Omnyfy\Vendor\Helper\User;
use Magento\Backend\Model\Auth\Session as AdminSession;

class Collection extends \Omnyfy\Vendor\Model\Resource\Collection\AbstractCollection
{
    const MAIN_TABLE_ALIAS = 'e';

    protected $_idFieldName = 'entity_id';

    protected $_flatEnabled = [];

    protected $_locationFlatState = null;

    protected $appState;

    protected $helper;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    protected $useHaversine = false;

    protected $_userHelper;

    protected $_adminSession;

    /**
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
     * @param \Magento\Framework\App\State $appState
     * @param \Omnyfy\Core\Helper\Data $omnyfyHelper
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        User $userHelper,
        AdminSession $adminSession,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Eav\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $appState,
        \Omnyfy\Vendor\Model\Indexer\Location\Flat\State $locationFlatState,
        \Omnyfy\Vendor\Helper\Data $helper,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $this->appState = $appState;
        $this->_userHelper = $userHelper;
        $this->_adminSession = $adminSession;
        $this->_locationFlatState = $locationFlatState;
        $this->helper = $helper;
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
            $connection
        );
    }

    /**
     * Construct
     */
    protected function _construct()
    {
        if ($this->isEnabledFlat()) {
            $this->_init('Omnyfy\Vendor\Model\Location', 'Omnyfy\Vendor\Model\Resource\Location\Flat');
        }
        else {
            $this->_init('Omnyfy\Vendor\Model\Location', 'Omnyfy\Vendor\Model\Resource\Location');
        }
    }

    protected function _init($model, $entityModel)
    {
        if ($this->isEnabledFlat()) {
            $entityModel = 'Omnyfy\Vendor\Model\Resource\Location\Flat';
        }
        return parent::_init($model, $entityModel);
    }

    protected function _prepareStaticFields()
    {
        if ($this->isEnabledFlat()) {
            return $this;
        }
        return parent::_prepareStaticFields();
    }

    protected function _initSelect()
    {
        if ($this->isEnabledFlat()) {
            $this->getSelect()->from(
                [self::MAIN_TABLE_ALIAS => $this->getEntity()->getFlatTableName()],
                null
            );
            $this->addAttributeToSelect($this->getResource()->getDefaultAttributes());
            /*
            if ($this->_locationFlatState->getFlatIndexerHelper()->isAddChildData()) {
                $this->getSelect()->where('e.is_child=?', 0);
                $this->addAttributeToSelect(['child_id', 'is_child']);
            }
            */
        } else {
            $this->getSelect()->from([self::MAIN_TABLE_ALIAS => $this->getEntity()->getEntityTable()]);
        }
        return $this;
    }

    protected function getBackendSession()
    {
        if (null == $this->_backendSession) {
            $this->_backendSession = \Magento\Framework\App\ObjectManager::getInstance()->get(\Magento\Backend\Model\Session::class);
        }
        return $this->_backendSession;
    }

    public function getNewEmptyItem()
    {
        $object = parent::getNewEmptyItem();
        if ($this->isEnabledFlat()) {
            $object->setIdFieldName($this->getEntity()->getIdFieldName());
        }
        return $object;
    }

    /**
     * Filter website
     *
     * @param int $websiteId
     * @return \Omnyfy\Vendor\Model\Resource\Location\Collection
     */
    public function filterWebsite($websiteId)
    {
        if (!is_array($websiteId)) {
            $websiteId = [0, $websiteId];
        }

        $subSql = 'SELECT location_id FROM ' . $this->getTable('omnyfy_vendor_profile_location')
            . ' WHERE profile_id IN (SELECT profile_id FROM '. $this->getTable('omnyfy_vendor_profile')
            . ' WHERE website_id IN (?))'
        ;
        $this->addFieldToFilter('entity_id',
            [
                'in' => new \Zend_Db_Expr($this->getConnection()->quoteInto($subSql, $websiteId))
            ]
        );

        return $this;
    }

    /**
     * Filter admin user
     *
     * @param int $adminUserId
     * @return \Omnyfy\Vendor\Model\Resource\Location\Collection
     */
    public function filterAdminUser($adminUserId)
    {
        $subSql = 'SELECT location_id FROM ' . $this->getTable('omnyfy_vendor_profile_location')
            . ' WHERE profile_id IN (SELECT profile_id FROM ' . $this->getTable('omnyfy_vendor_profile_admin_user')
            . ' WHERE admin_user_id = ?)'
        ;

        $this->addFieldToFilter('e.entity_id',
            [
                'in' => new \Zend_Db_Expr($this->getConnection()->quoteInto($subSql, $adminUserId))
            ]
        );

        return $this;
    }

    /**
     * Filter profile
     *
     * @param array $profileIds
     * @return \Omnyfy\Vendor\Model\Resource\Location\Collection
     */
    public function filterProfile($profileIds)
    {
        $plTable = $this->getTable('omnyfy_vendor_profile_location');
        $subSql = 'SELECT location_id FROM '.$plTable. ' WHERE profile_id IN (?)';

        $this->addFieldToFilter('e.entity_id',
            [
                'in' => new \Zend_Db_Expr($this->getConnection()->quoteInto($subSql, $profileIds))
            ]
        );

        return $this;
    }

    /**
     * Join vendor info
     *
     * @return \Omnyfy\Vendor\Model\Resource\Location\Collection
     */
    public function joinVendorInfo()
    {
        if (!$this->getFlag('has_joined_vendor')) {
            $this->getSelect()
                ->join(
                    ['vendor' => $this->getTable('omnyfy_vendor_vendor_entity')],
                    'e.vendor_id = vendor.entity_id',
                    ['vendor_name' => 'vendor.name']
                )
            ;

            $this->setFlag('has_joined_vendor', 1);
        }

        return $this;
    }

    /**
     * Hook for operations before rendering filters
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        parent::_renderFiltersBefore();

        if (\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE != $this->appState->getAreaCode()) {
            return;
        }

        $vendorInfo = $this->getBackendSession()->getVendorInfo();

        $currentUser = $this->_adminSession->getUser();

        $userVendor = $this->_userHelper->getUserVendor($currentUser->getUserId());

        // If this is set, this will override what is set inside the vendor
        $userStores = $this->_userHelper->getUserStores($currentUser->getUserId());

        // If vendor info is empty and no stores are set on user, display everything
        if (empty($vendorInfo) && (!$userStores || in_array(0, $userStores))) {
            return;
        }

        if (!empty($vendorInfo) && (!$userStores || in_array(0, $userStores)) && !$vendorInfo['vendor_id']) {
            return;
        }

        // If vendor info is not empty
        if (!empty($vendorInfo)) {
            if (empty($vendorInfo['website_ids'])) {
                $vendorInfo['website_ids'] = [-1];
            }
            if (empty($vendorInfo['store_ids'])) {
                $vendorInfo['store_ids'] = [-1];
            }
            if (empty($vendorInfo['profile_ids'])) {
                $vendorInfo['profile_ids'] = [-1];
            }
            if (empty($vendorInfo['location_ids'])) {
                $vendorInfo['location_ids'] = [-1];
            }
            if (empty($vendorInfo['vendor_id'])) {
                $vendorInfo['vendor_id'] = 0;
            }
        }

        $this->_logger->debug('here2: '. get_class($this));

        if (!empty($vendorInfo)) {
            if ($vendorInfo['vendor_id']) {
                $this->addFieldToFilter('vendor_id', $vendorInfo['vendor_id']);
            }
            if ($userStores) {
                $this->filterWebsite($userStores);
            } else{
                $this->filterWebsite($vendorInfo['website_ids']);
            }
            if (!isset($vendorInfo['is_vendor_admin']) || empty($vendorInfo['is_vendor_admin'])) {
                //filter location by website and user
                if ($userStores) {
                    $this->filterWebsite($userStores);
                } else{
                    $this->filterWebsite($vendorInfo['website_ids']);
                }

                $this->filterProfile($vendorInfo['profile_ids']);
            }

            $this->_logger->debug('filtered vendor location collection', $vendorInfo);
        }
    }

    /**
     * Add keyword filter
     *
     * @param string $keyword
     * @return \Omnyfy\Vendor\Model\Resource\Location\Collection
     */
    public function addKeywordFilter($keyword)
    {
        if (!$this->getFlag('has_keyword_filter')) {
            $this->addFieldToFilter('location_name', ['like' => '%' . $keyword . '%']);

            $this->setFlag('has_keyword_filter', 1);
        }

        return $this;
    }

    public function getFlatState()
    {
        return $this->_locationFlatState;
    }

    public function isEnabledFlat()
    {
        if (!isset($this->_flatEnabled[$this->getStoreId()])) {
            $this->_flatEnabled[$this->getStoreId()] = $this->getFlatState()->isAvailable();
        }
        return $this->_flatEnabled[$this->getStoreId()];
    }

    public function setEntity($entity)
    {
        if ($this->isEnabledFlat() && $entity instanceof \Magento\Framework\Model\ResourceModel\Db\AbstractDb) {
            $this->_entity = $entity;
            return $this;
        }
        return parent::setEntity($entity);
    }

    public function setStore($store)
    {
        parent::setStore($store);
        if ($this->isEnabledFlat()) {
            $this->getEntity()->setStoreId($this->getStoreId());
        }
        return $this;
    }

    public function _loadAttributes($printQuery = false, $logQuery = false)
    {
        if ($this->isEnabledFlat()) {
            return $this;
        }
        return parent::_loadAttributes($printQuery, $logQuery);
    }

    public function addAttributeToSelect($attribute, $joinType = false)
    {
        if ($this->isEnabledFlat()) {
            if (!is_array($attribute)) {
                $attribute = [$attribute];
            }
            foreach ($attribute as $attributeCode) {
                if ($attributeCode == '*') {
                    foreach ($this->getEntity()->getAllTableColumns() as $column) {
                        $this->getSelect()->columns('e.' . $column);
                        $this->_selectAttributes[$column] = $column;
                        $this->_staticFields[$column] = $column;
                    }
                } else {
                    $columns = $this->getEntity()->getAttributeForSelect($attributeCode);
                    if ($columns) {
                        foreach ($columns as $alias => $column) {
                            $this->getSelect()->columns([$alias => 'e.' . $column]);
                            $this->_selectAttributes[$column] = $column;
                            $this->_staticFields[$column] = $column;
                        }
                    }
                }
            }
            return $this;
        }
        return parent::addAttributeToSelect($attribute, $joinType);
    }

    public function addAttributeToSort($attribute, $dir = self::SORT_ORDER_ASC)
    {
        if ($this->isEnabledFlat()) {
            $column = $this->getEntity()->getAttributeSortColumn($attribute);

            if ($column) {
                $this->getSelect()->order("e.{$column} {$dir}");
            } elseif (isset($this->_joinFields[$attribute])) {
                $this->getSelect()->order($this->_getAttributeFieldName($attribute) . ' ' . $dir);
            }

            return $this;
        } else {
            $attrInstance = $this->getEntity()->getAttribute($attribute);
            if ($attrInstance && $attrInstance->usesSource()) {
                $attrInstance->getSource()->addValueSortToCollection($this, $dir);
                return $this;
            }
        }

        return parent::addAttributeToSort($attribute, $dir);
    }
    /**
     * Add status filter
     *
     * @param int $status
     * @return $this
     */
    public function addStatusFilter($status)
    {
        if (!$this->getFlag('has_status_filter')) {
            $this->addFieldToFilter('status', $status);

            $this->setFlag('has_status_filter', 1);
        }

        return $this;
    }

    public function addDistanceFilter($latitude, $longitude, $limitInKm)
    {
        if (!$this->getFlag('has_distance_filter')) {
            $express = $this->helper->getDistanceExpression($latitude, $longitude, $this->useHaversine);

            $this->addExpressionAttributeToSelect(
                'distance',
                $express,
                ['rad_lon', 'rad_lat', 'cos_lat', 'sin_lat']
            );

            $this->getSelect()->where(
                '(' . $express . ') < ?',
                $limitInKm
            );

            $this->setFlag('has_distance_filter', true);
        }

        return $this;
    }

    public function addProductIdFilter($productId)
    {
        if (!$this->getFlag('has_product_id_filter')) {
            $this->getSelect()
                ->joinLeft(
                    array('second' => 'omnyfy_vendor_inventory'),
                    'e.entity_id = second.location_id'
                )
                ->where("second.product_id=".$productId)
                ->order('e.vendor_id DESC');

            $this->setFlag('has_product_id_filter', true);
        }

        return $this;
    }

    public function filterProductIdRestrict($productId)
    {
        if (!$this->getFlag('has_restrict_product_id_filter')) {
            $conn = $this->getConnection();

            $sql = 'SELECT location_id FROM '. $this->getTable('omnyfy_vendor_inventory');

            if (!is_array($productId) && ! ($productId instanceof \Zend_Db_Expr)) {
                $productId = array($productId);
            }

            $sql .= $conn->quoteInto(' WHERE product_id IN (?)', $productId);

            $this->addFieldToFilter('entity_id',
                [
                    'in' => new \Zend_Db_Expr($sql)
                ]);

            $this->setFlag('has_restrict_product_id_filter', true);
        }
    }
}