<?php

namespace Omnyfy\Vendor\Helper\Location\Flat;

class Indexer extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Retrieve vendor location flat columns array in old format (used before MMDB support)
     *
     * @return array
     */
    protected $_attributes;

    /**
     * @var array
     */
    protected $_indexes;

    /**
     * Required system attributes for preload
     *
     * @var array
     */
    protected $_systemAttributes = [];

    /**
     * @var int
     */
    protected $_entityTypeId;

    /**
     * Resource instance
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resource;

    /**
     * @var \Omnyfy\Vendor\Model\Resource\Location\ConfigFactory
     */
    protected $_configFactory;

    /**
     * @var \Magento\Eav\Model\Entity\AttributeFactory
     */
    protected $_attributeFactory;

    /**
     * EAV Config instance
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var array
     */
    protected $_flatAttributeGroups = [];

    /**
     * @var array
     */
    protected $_columns;

    /**
     * @var array
     */
    protected $_attributeCodes;

    protected $_addFilterableAttrs;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory
     * @param \Omnyfy\Vendor\Model\Resource\Location\ConfigFactory $configFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory,
        \Omnyfy\Vendor\Model\Resource\Location\ConfigFactory $configFactory,
        $addFilterableAttrs = false
    ) {
        $this->_configFactory = $configFactory;
        $this->_resource = $resource;
        $this->_eavConfig = $eavConfig;
        $this->_attributeFactory = $attributeFactory;
        $this->_addFilterableAttrs = $addFilterableAttrs;

        parent::__construct($context);
    }

    /**
     * Retrieve entity type
     *
     * @return string
     */
    public function getEntityType()
    {
        return \Omnyfy\Vendor\Model\Location::ENTITY;
    }

    /**
     * Retrieve Location Entity Type Id
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        if ($this->_entityTypeId === null) {
            $this->_entityTypeId = $this->_configFactory->create()->getEntityTypeId();
        }
        return $this->_entityTypeId;
    }

    /**
     * Retrieve attribute objects for flat
     *
     * @return array
     */
    public function getAttributes()
    {
        if ($this->_attributes === null) {
            $this->_attributes = [];
            $attributeCodes = $this->getAttributeCodes();
            $entity = $this->_eavConfig->getEntityType($this->getEntityType())->getEntity();

            foreach ($attributeCodes as $attributeCode) {
                $attribute = $this->_eavConfig->getAttribute(
                    $this->getEntityType(),
                    $attributeCode
                )->setEntity(
                    $entity
                );
                try {
                    // check if exists source and backend model.
                    // To prevent exception when some module was disabled
                    $attribute->usesSource() && $attribute->getSource();
                    $attribute->getBackend();
                    $this->_attributes[$attributeCode] = $attribute;
                } catch (\Exception $e) {
                    $this->_logger->critical($e);
                }
            }
        }
        return $this->_attributes;
    }

    /**
     * Retrieve attribute codes using for flat
     *
     * @return array
     */
    public function getAttributeCodes()
    {
        if ($this->_attributeCodes === null) {
            $connection = $this->_resource->getConnection();
            $this->_attributeCodes = [];

            $bind = [
                'entity_type_id' => $this->getEntityTypeId(),
            ];

            $select = $connection->select()->from(
                ['main_table' => $this->getTable('eav_attribute')]
            )->join(
                ['additional_table' => $this->getTable('omnyfy_vendor_eav_attribute')],
                'additional_table.attribute_id = main_table.attribute_id'
            )->where(
                'main_table.entity_type_id = :entity_type_id'
            );

            $attributesData = $connection->fetchAll($select, $bind);
            $this->_eavConfig->importAttributesData($this->getEntityType(), $attributesData);

            foreach ($attributesData as $data) {
                $this->_attributeCodes[$data['attribute_id']] = $data['attribute_code'];
            }
            unset($attributesData);
        }
        return $this->_attributeCodes;
    }

    /**
     * Returns table name
     *
     * @param string|array $name
     * @return string
     */
    public function getTable($name)
    {
        return $this->_resource->getTableName($name);
    }

    /**
     * Retrieve Catalog Product Flat Table name
     *
     * @param int $storeId
     * @return string
     */
    public function getFlatTableName($storeId)
    {
        return sprintf('%s_%s', $this->getTable('omnyfy_vendor_location_flat'), $storeId);
    }

    /**
     * Retrieve loaded attribute by code
     *
     * @param string $attributeCode
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Magento\Eav\Model\Entity\Attribute
     */
    public function getAttribute($attributeCode)
    {
        $attributes = $this->getAttributes();
        if (!isset($attributes[$attributeCode])) {
            $attribute = $this->_attributeFactory->create();
            $attribute->loadByCode($this->getEntityTypeId(), $attributeCode);
            if (!$attribute->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(__('Invalid attribute %1', $attributeCode));
            }
            $entity = $this->_eavConfig->getEntityType($this->getEntityType())->getEntity();
            $attribute->setEntity($entity);
            return $attribute;
        }
        return $attributes[$attributeCode];
    }

    /**
     * Get table structure for temporary eav tables
     *
     * @param array $attributes
     * @return array
     */
    public function getTablesStructure(array $attributes)
    {
        $eavAttributes = [];
        $flatColumnsList = $this->getFlatColumns();
        foreach ($attributes as $attribute) {
            $eavTable = $attribute->getBackend()->getTable();
            $attributeCode = $attribute->getAttributeCode();
            if (isset($flatColumnsList[$attributeCode])) {
                $eavAttributes[$eavTable][$attributeCode] = $attribute;
            }
        }

        return $eavAttributes;
    }

    /**
     * Retrieve catalog product flat table columns array
     *
     * @return array
     */
    public function getFlatColumns()
    {
        if ($this->_columns === null) {
            $this->_columns = $this->getFlatColumnsDdlDefinition();
            foreach ($this->getAttributes() as $attribute) {
                /** @var $attribute \Magento\Eav\Model\Entity\Attribute\AbstractAttribute */
                $columns = $attribute->getFlatColumns();
                if ($columns !== null) {
                    $this->_columns = array_merge($this->_columns, $columns);
                }
            }
        }
        return $this->_columns;
    }

    /**
     * Retrieve vendor location flat columns array in DDL format
     *
     * @return array
     */
    public function getFlatColumnsDdlDefinition()
    {
        $columns = [];
        $columns['entity_id'] = [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length' => null,
            'unsigned' => true,
            'nullable' => false,
            'default' => false,
            'primary' => true,
            'comment' => 'Location Id',
        ];
        $columns['attribute_set_id'] = [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            'length' => 5,
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
            'comment' => 'Attribute Set ID',
        ];
        $columns['vendor_type_id'] = [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length' => 32,
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
            'comment' => 'Vendor Type Id',
        ];
        return $columns;
    }

    /**
     * Retrieve catalog product flat table indexes array
     *
     * @return array
     */
    public function getFlatIndexes()
    {
        if ($this->_indexes === null) {
            $this->_indexes = [];
            $this->_indexes['PRIMARY'] = [
                'type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_PRIMARY,
                'fields' => ['entity_id'],
            ];

            foreach ($this->getAttributes() as $attribute) {
                /** @var $attribute \Magento\Eav\Model\Entity\Attribute */
                $indexes = $attribute->getFlatIndexes();
                if ($indexes !== null) {
                    $this->_indexes = array_merge($this->_indexes, $indexes);
                }
            }
        }
        return $this->_indexes;
    }

    public function isAddFilterableAttributes()
    {
        return $this->_addFilterableAttrs;
    }
}
