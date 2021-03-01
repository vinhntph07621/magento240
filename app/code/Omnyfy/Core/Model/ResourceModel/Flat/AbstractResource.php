<?php

namespace Omnyfy\Core\Model\ResourceModel\Flat;

use Magento\Eav\Model\ResourceModel\Attribute\DefaultEntityAttributes\ProviderInterface as DefaultAttributesProvider;

abstract class AbstractResource extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb implements DefaultAttributesProvider
{

    /**
     * @var int
     */
    protected $_storeId;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param type $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Eav\Model\Config $eavConfig,
        string $connectionName = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_eavConfig = $eavConfig;

        parent::__construct($context, $connectionName);
    }

    /**
     * Retrieve default entity static attributes
     *
     * @return string[]
     */
    abstract public function getDefaultAttributes();

    /**
     * Construct
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(static::FLAT_TABLE_NAME, 'entity_id');
        $this->setStoreId(null);
    }

    /**
     * Retrieve Flat Table name
     *
     * @param mixed $store
     * @return string
     */
    public function getFlatTableName($store = null)
    {
        if ($store === null) {
            $store = $this->getStoreId();
        }
        return $this->getTable(static::FLAT_TABLE_NAME . '_' . $store);
    }

    /**
     * Retrieve store for resource model
     *
     * @return int
     */
    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
     * Set store for resource model
     *
     * @param null|string|bool|int|Store $store
     * @return $this
     */
    public function setStoreId($store)
    {
        if (is_int($store)) {
            $this->_storeId = $store;
        } else {
            $this->_storeId = $this->_storeManager->getStore()->getId();
        }
        if (empty($this->_storeId)) {
            $defaultStore = $this->_storeManager->getDefaultStoreView();
            if ($defaultStore) {
                $this->_storeId = (int)$defaultStore->getId();
            }
        }
        return $this;
    }

    /**
     * Retrieve attribute columns for collection select
     *
     * @param string $attributeCode
     * @return array|null
     */
    public function getAttributeForSelect($attributeCode)
    {
        $describe = $this->getConnection()->describeTable($this->getFlatTableName());
        if (!isset($describe[$attributeCode])) {
            return null;
        }
        $columns = [$attributeCode => $attributeCode];

        $attributeIndex = sprintf('%s_value', $attributeCode);
        if (isset($describe[$attributeIndex])) {
            $columns[$attributeIndex] = $attributeIndex;
        }

        return $columns;
    }

    /**
     * Retrieve Attribute Sort column name
     *
     * @param string $attributeCode
     * @return string
     */
    public function getAttributeSortColumn($attributeCode)
    {
        $describe = $this->getConnection()->describeTable($this->getFlatTableName());
        if (!isset($describe[$attributeCode])) {
            return null;
        }
        $attributeIndex = sprintf('%s_value', $attributeCode);
        if (isset($describe[$attributeIndex])) {
            return $attributeIndex;
        }
        return $attributeCode;
    }

    /**
     * Retrieve Flat Table columns list
     *
     * @return array
     */
    public function getAllTableColumns()
    {
        $describe = $this->getConnection()->describeTable($this->getFlatTableName());
        return array_keys($describe);
    }

    /**
     * Check whether the attribute is a real field in entity table
     * Rewrited for EAV Collection
     *
     * @param integer|string|\Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute
     * @return bool
     */
    public function isAttributeStatic($attribute)
    {
        $attributeCode = null;
        if ($attribute instanceof \Magento\Eav\Model\Entity\Attribute\AttributeInterface) {
            $attributeCode = $attribute->getAttributeCode();
        } elseif (is_string($attribute)) {
            $attributeCode = $attribute;
        } elseif (is_numeric($attribute)) {
            $attributeCode = $this->getAttribute($attribute)->getAttributeCode();
        }

        if ($attributeCode) {
            $columns = $this->getAllTableColumns();
            if (in_array($attributeCode, $columns)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve entity id field name in entity table
     * Rewrote for EAV collection compatibility
     *
     * @return string
     */
    public function getEntityIdField()
    {
        return $this->getIdFieldName();
    }

    /**
     * Retrieve entity type id
     *
     * @return int
     */
    public function getTypeId()
    {
        return $this->_eavConfig->getEntityType(static::ENTITY_TYPE)->getEntityTypeId();
    }

    /**
     * Retrieve attribute instance
     * Special for non static flat table
     *
     * @param mixed $attribute
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    public function getAttribute($attribute)
    {
        return $this->_eavConfig->getAttribute(static::ENTITY_TYPE, $attribute);
    }

    /**
     * Retrieve main resource table name
     *
     * @return string
     */
    public function getMainTable()
    {
        return $this->getFlatTableName($this->getStoreId());
    }

}
