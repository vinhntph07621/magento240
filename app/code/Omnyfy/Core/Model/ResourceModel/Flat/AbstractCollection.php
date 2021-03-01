<?php

namespace Omnyfy\Core\Model\ResourceModel\Flat;

abstract class AbstractCollection extends \Magento\Eav\Model\Entity\Collection\AbstractCollection
{

    const MAIN_TABLE_ALIAS = 'e';

    /**
     * Current scope (store Id)
     *
     * @var int
     */
    protected $_storeId;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * @var \Omnyfy\Core\Helper\Data
     */
    protected $_omnyfyHelper;

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
     * @param \Omnyfy\Core\Helper\Data $omnyfyHelper
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
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
        \Magento\Framework\App\State $appState,
        \Omnyfy\Core\Helper\Data $omnyfyHelper,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_appState = $appState;
        $this->_omnyfyHelper = $omnyfyHelper;

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
            $connection
        );
    }

    /**
     * Is flat enabled?
     *
     * @return bool
     */
    public function isEnabledFlat()
    {
        return $this->_omnyfyHelper->isEnabledFlat();
    }

    /**
     * Standard resource collection initialization
     * Needed for child classes
     *
     * @param string $model
     * @param string $entityModel
     * @return $this
     */
    protected function _init($model, $entityModel)
    {
        if ($this->isEnabledFlat()) {
            $entityModel .= '\Flat';
        }

        return parent::_init($model, $entityModel);
    }

    /**
     * Prepare static entity fields
     *
     * @return $this
     */
    protected function _prepareStaticFields()
    {
        if ($this->isEnabledFlat()) {
            return $this;
        }
        return parent::_prepareStaticFields();
    }

    /**
     * Initialize collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        if ($this->isEnabledFlat()) {
            $this->getSelect()->from([self::MAIN_TABLE_ALIAS => $this->getEntity()->getFlatTableName()]);
        } else {
            $this->getSelect()->from([self::MAIN_TABLE_ALIAS => $this->getEntity()->getEntityTable()]);
        }

        return $this;
    }

    /**
     * Load attributes into loaded entities
     *
     * @param bool $printQuery
     * @param bool $logQuery
     * @return $this
     */
    public function _loadAttributes($printQuery = false, $logQuery = false)
    {
        if ($this->isEnabledFlat()) {
            return $this;
        }
        return parent::_loadAttributes($printQuery, $logQuery);
    }

    /**
     * Add attribute to entities in collection
     * If $attribute=='*' select all attributes
     *
     * @param array|string|integer|\Magento\Framework\App\Config\Element $attribute
     * @param bool|string $joinType
     * @return $this
     */
    public function addAttributeToSelect($attribute, $joinType = false)
    {
        if ($this->isEnabledFlat()) {
            if (!is_array($attribute)) {
                $attribute = [$attribute];
            }
            foreach ($attribute as $attributeCode) {
                if ($attributeCode == '*') {
                    foreach ($this->getEntity()->getAllTableColumns() as $column) {
                        $this->getSelect()->columns(self::MAIN_TABLE_ALIAS . '.' . $column);
                        $this->_selectAttributes[$column] = $column;
                        $this->_staticFields[$column] = $column;
                    }
                } else {
                    $columns = $this->getEntity()->getAttributeForSelect($attributeCode);
                    if ($columns) {
                        foreach ($columns as $alias => $column) {
                            $this->getSelect()->columns([$alias => self::MAIN_TABLE_ALIAS . '.' . $column]);
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

    /**
     * Retrieve collection empty item
     * Redeclared for specifying id field name without getting resource model inside model
     *
     * @return \Magento\Framework\DataObject
     */
    public function getNewEmptyItem()
    {
        $object = parent::getNewEmptyItem();
        if ($this->isEnabledFlat()) {
            $object->setIdFieldName($this->getEntity()->getIdFieldName());
        }
        return $object;
    }

    /**
     * Set entity to use for attributes
     *
     * @param \Magento\Eav\Model\Entity\AbstractEntity $entity
     * @return $this
     */
    public function setEntity($entity)
    {
        if ($this->isEnabledFlat() && $entity instanceof \Magento\Framework\Model\ResourceModel\Db\AbstractDb) {
            $this->_entity = $entity;
            return $this;
        }
        return parent::setEntity($entity);
    }

    /**
     * Set store scope
     *
     * @param int|string|\Magento\Store\Model\Store $store
     * @return $this
     */
    public function setStore($store)
    {
        $storeId = $this->_storeManager->getStore($store)->getId();
        $this->setStoreId($storeId);

        if ($this->isEnabledFlat()) {
            $this->getEntity()->setStoreId($storeId);
        }

        return $this;
    }

    /**
     * Set store scope
     *
     * @param int|string|\Magento\Store\Model\Store $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        if ($storeId instanceof \Magento\Store\Model\Store) {
            $storeId = $storeId->getId();
        }
        $this->_storeId = (int)$storeId;
        return $this;
    }

    /**
     * Return current store id
     *
     * @return int
     */
    public function getStoreId()
    {
        if ($this->_storeId === null) {
            $this->setStoreId($this->_storeManager->getStore()->getId());
        }
        return $this->_storeId;
    }

    /**
     * Retrieve default store id
     *
     * @return int
     */
    public function getDefaultStoreId()
    {
        return \Magento\Store\Model\Store::DEFAULT_STORE_ID;
    }

    /**
     * Map join field
     *
     * @param string $alias
     * @param string $tableAlias
     * @param string $field
     * @return $this
     */
    public function mapJoinField($alias, $tableAlias, $field)
    {
        $this->_joinFields[$alias] = [
            'table' => $tableAlias,
            'field' => $field
        ];

        return $this;
    }

}
