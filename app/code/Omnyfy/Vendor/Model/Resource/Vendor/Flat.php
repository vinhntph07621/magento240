<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-06-05
 * Time: 14:13
 */
namespace Omnyfy\Vendor\Model\Resource\Vendor;

use Magento\Store\Model\Store;
use Magento\Eav\Model\ResourceModel\Attribute\DefaultEntityAttributes\ProviderInterface as DefaultAttributesProvider;

class Flat extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb implements DefaultAttributesProvider
{
    protected $_storeId;

    /**
     * @var \Omnyfy\Vendor\Model\Vendor\Config
     */
    protected $_vendorConfig;

    protected $_storeManager;

    /**
     * @var \Omnyfy\Vendor\Model\Vendor\Attribute\DefaultAttributes
     */
    protected $defaultAttributes;

    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\Vendor\Model\Vendor\Config $vendorConfig,
        \Omnyfy\Vendor\Model\Vendor\Attribute\DefaultAttributes $defaultAttributes,
        $connectionName = null
    ) {
        $this->_storeManager = $storeManager;
        $this->_vendorConfig = $vendorConfig;
        $this->defaultAttributes = $defaultAttributes;
        parent::__construct($context, $connectionName);
    }

    protected function _construct()
    {
        $this->_init('omnyfy_vendor_vendor_flat', 'entity_id');
        $this->setStoreId(null);
    }

    public function getStoreId()
    {
        return $this->_storeId;
    }

    /**
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
        return $this->getTable('omnyfy_vendor_vendor_flat_' . $store);
    }

    public function getTypeId()
    {
        return $this->_vendorConfig->getEntityType(\Omnyfy\Vendor\Model\Vendor::ENTITY)->getEntityTypeId();
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
     * Retrieve attribute instance
     * Special for non static flat table
     *
     * @param mixed $attribute
     * @return \Magento\Eav\Model\Entity\Attribute\AbstractAttribute
     */
    public function getAttribute($attribute)
    {
        return $this->_vendorConfig->getAttribute(\Omnyfy\Vendor\Model\Vendor::ENTITY, $attribute);
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

    /**
     * Retrieve default entity static attributes
     *
     * @return string[]
     */
    public function getDefaultAttributes()
    {
        return array_unique(
            array_merge(
                $this->defaultAttributes->getDefaultAttributes(),
                [$this->getEntityIdField()]
            )
        );
    }
}
 