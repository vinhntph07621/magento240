<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 6/6/17
 * Time: 11:00 AM
 */

namespace Omnyfy\Vendor\Model\Resource\Vendor;

use Omnyfy\Vendor\Model\Resource\Collection\AbstractCollection;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\State as AppState;
use Magento\Store\Model\Store;
use Omnyfy\Vendor\Helper\User;
use Magento\Backend\Model\Auth\Session as AdminSession;

class Collection extends AbstractCollection
{
    const MAIN_TABLE_ALIAS = 'e';

    protected $_idFieldName = 'entity_id';

    protected $_flatEnabled = [];

    protected $_vendorWebsiteTable;

    protected $appState;

    protected $helper;

    protected $backendSession;

    protected $useHaversine = false;

    /**
     * @var \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\State
     */
    protected $_vendorFlatState = null;

    protected $_userHelper;

    protected $_adminSession;

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
        $connection = null
    )
    {
        $this->appState = $appState;
        $this->_vendorFlatState = $vendorFlatState;
        $this->helper = $helper;
        $this->_userHelper = $userHelper;
        $this->_adminSession = $adminSession;
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

    protected function _construct()
    {
        if ($this->isEnabledFlat()) {
            $this->_init('Omnyfy\Vendor\Model\Vendor', 'Omnyfy\Vendor\Model\Resource\Vendor\Flat');
        }
        else {
            $this->_init('Omnyfy\Vendor\Model\Vendor', 'Omnyfy\Vendor\Model\Resource\Vendor');
        }
        $this->_initTables();
    }

    protected function _init($model, $entityModel)
    {
        if ($this->isEnabledFlat()) {
            $entityModel = 'Omnyfy\Vendor\Model\Resource\Vendor\Flat';
        }
        return parent::_init($model, $entityModel);
    }

    protected function _initTables()
    {
        $this->_vendorWebsiteTable = $this->getResource()->getTable('omnyfy_vendor_vendor_profile');
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
            )->columns(
                ['status' => new \Zend_Db_Expr(\Omnyfy\Vendor\Api\Data\VendorInterface::STATUS_ENABLED)]
            );
            $this->addAttributeToSelect($this->getResource()->getDefaultAttributes());
            if ($this->_vendorFlatState->getFlatIndexerHelper()->isAddChildData()) {
                $this->getSelect()->where('e.is_child=?', 0);
                $this->addAttributeToSelect(['child_id', 'is_child']);
            }
        } else {
            $this->getSelect()->from([self::MAIN_TABLE_ALIAS => $this->getEntity()->getEntityTable()]);
        }
        return $this;
    }

    protected function getBackendSession()
    {
        if (null == $this->backendSession) {
            $this->backendSession = \Magento\Framework\App\ObjectManager::getInstance()->get(BackendSession::class);
        }
        return $this->backendSession;
    }

    protected function _renderFiltersBefore() {
        parent::_renderFiltersBefore();

        if (FrontNameResolver::AREA_CODE != $this->appState->getAreaCode()) {
            return;
        }

        $vendorInfo = $this->getBackendSession()->getVendorInfo();

        $currentUser = $this->_adminSession->getUser();

        if (!$currentUser) {
            return;
        }

        $userVendor = $this->_userHelper->getUserVendor($currentUser->getUserId());

        // If this is set, this will override what is set inside the vendor
        $userStores = $this->_userHelper->getUserStores($currentUser->getUserId());

        if (empty($vendorInfo) && (!$userStores || in_array(0, $userStores))) {
            return;
        }

        if (!empty($vendorInfo) && (!$userStores || in_array(0, $userStores)) && !$vendorInfo['vendor_id']) {
            return;
        }

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

        if (empty($vendorInfo) && $userStores) {
            $this->filterWebsite($userStores);
        }
        elseif (!empty($vendorInfo)) {
            if ($vendorInfo['vendor_id']) {
                $this->addFieldToFilter('entity_id', $vendorInfo['vendor_id']);
            }
            if ($userStores) {
                $this->filterWebsite($userStores);
            } else {
                $this->filterWebsite($vendorInfo['website_ids']);
            }
            if (!isset($vendorInfo['is_vendor_admin']) || empty($vendorInfo['is_vendor_admin'])) {
                if ($userStores) {
                    $this->filterWebsite($userStores);
                } else {
                    $this->filterWebsite($vendorInfo['website_ids']);
                }
            }
        }

        $this->_logger->debug('filtered vendor collection', $vendorInfo);
    }

    public function filterWebsite($websiteId)
    {
        $subSql = 'SELECT vendor_id FROM ' . $this->getTable('omnyfy_vendor_profile')
                . ' WHERE website_id IN (?)'
        ;
        $this->addFieldToFilter('entity_id',
            [
                'in' => new \Zend_Db_Expr($this->getConnection()->quoteInto($subSql, $websiteId))
            ]
        );
        return $this;
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
     * @return \Omnyfy\Vendor\Model\Indexer\Vendor\Flat\State
     */
    public function getFlatState()
    {
        return $this->_vendorFlatState;
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

    public function addIdFilter($vendorId, $exclude = false)
    {
        if (empty($vendorId)) {
            $this->_setIsLoaded(true);
            return $this;
        }
        if (is_array($vendorId)) {
            if (!empty($vendorId)) {
                if ($exclude) {
                    $condition = ['nin' => $vendorId];
                } else {
                    $condition = ['in' => $vendorId];
                }
            } else {
                $condition = '';
            }
        } else {
            if ($exclude) {
                $condition = ['neq' => $vendorId];
            } else {
                $condition = $vendorId;
            }
        }
        $this->addFieldToFilter('entity_id', $condition);
        return $this;
    }

    public function addStoreFilter($store = null)
    {
        if ($store === null) {
            $store = $this->getStoreId();
        }
        $store = $this->_storeManager->getStore($store);

        if ($store->getId() != Store::DEFAULT_STORE_ID) {
            $this->setStoreId($store);
            //$this->_productLimitationFilters['store_id'] = $store->getId();
            //$this->_applyProductLimitations();
        }

        return $this;
    }

    public function addDistanceFilter($latitude, $longitude, $limitInKm)
    {
        if ($this->getFlag('has_distance_filter')) {
            return $this;
        }

        $this->addDistanceJoin($latitude, $longitude);

        $this->getSelect()->where('distance < ? ', $limitInKm);

        $this->setFlag('has_distance_filter', true);

        return $this;
    }

    protected function addDistanceJoin($latitude, $longitude)
    {
        if ($this->getFlag('has_distance_join')) {
            return $this;
        }

        $table = $this->getTable('omnyfy_vendor_location_entity');
        if ($this->helper->isEnabledLocationFlat($this->getStoreId())) {
            $table = $this->getTable('omnyfy_vendor_location_flat_' . $this->getStoreId());
        }
        $sql = '(SELECT vendor_id, MIN('
            . $this->helper->getDistanceExpression($latitude, $longitude, $this->useHaversine) . ') as distance'
            . ' FROM ' . $table
            . ' GROUP BY vendor_id)';

        $this->getSelect()->joinInner(
            [ 'l' =>  new \Zend_Db_Expr($sql)],
            'e.entity_id=l.vendor_id',
            ['distance' => 'l.distance']
        );

        $this->setFlag('has_distance_join', true);

        return $this;
    }


}