<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-05-08
 * Time: 16:48
 */
namespace Omnyfy\Vendor\Model\Resource\Vendor\Indexer;

use Omnyfy\Vendor\Api\Data\VendorInterface;

abstract class AbstractIndexer extends \Magento\Indexer\Model\ResourceModel\AbstractResource
{
    /**
     * Eav config
     *
     * @var \Magento\Eav\Model\Config
     */
    protected $_eavConfig;

    /**
     * @var \Magento\Framework\EntityManager\MetadataPool
     */
    protected $metadataPool;

    /**
     * Class constructor
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Framework\Indexer\Table\StrategyInterface $tableStrategy
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param string $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Framework\Indexer\Table\StrategyInterface $tableStrategy,
        \Magento\Eav\Model\Config $eavConfig,
        $connectionName = null
    ) {
        $this->_eavConfig = $eavConfig;
        parent::__construct($context, $tableStrategy, $connectionName);
    }

    /**
     * Retrieve vendor attribute instance by attribute code
     *
     * @param string $attributeCode
     * @return \Omnyfy\Vendor\Model\Resource\Vendor\Eav\Attribute
     */
    protected function _getAttribute($attributeCode)
    {
        return $this->_eavConfig->getAttribute(\Omnyfy\Vendor\Model\Vendor::ENTITY, $attributeCode);
    }

    /**
     * Add attribute join condition to select and return \Zend_Db_Expr
     * attribute value definition
     * If $condition is not empty apply limitation for select
     *
     * @param \Magento\Framework\DB\Select $select
     * @param string $attrCode              the attribute code
     * @param string|\Zend_Db_Expr $entity   the entity field or expression for condition
     * @param string|\Zend_Db_Expr $store    the store field or expression for condition
     * @param \Zend_Db_Expr $condition       the limitation condition
     * @param bool $required                if required or has condition used INNER join, else - LEFT
     * @return \Zend_Db_Expr                 the attribute value expression
     */
    protected function _addAttributeToSelect($select, $attrCode, $entity, $store, $condition = null, $required = false)
    {
        $attribute = $this->_getAttribute($attrCode);
        $attributeId = $attribute->getAttributeId();
        $attributeTable = $attribute->getBackend()->getTable();
        $connection = $this->getConnection();
        $joinType = $condition !== null || $required ? 'join' : 'joinLeft';
        $productIdField = $this->getMetadataPool()->getMetadata(ProductInterface::class)->getLinkField();

        if ($attribute->isScopeGlobal()) {
            $alias = 'ta_' . $attrCode;
            $select->{$joinType}(
                [$alias => $attributeTable],
                "{$alias}.{$productIdField} = {$entity} AND {$alias}.attribute_id = {$attributeId}" .
                " AND {$alias}.store_id = 0",
                []
            );
            $expression = new \Zend_Db_Expr("{$alias}.value");
        } else {
            $dAlias = 'tad_' . $attrCode;
            $sAlias = 'tas_' . $attrCode;

            $select->{$joinType}(
                [$dAlias => $attributeTable],
                "{$dAlias}.{$productIdField} = {$entity} AND {$dAlias}.attribute_id = {$attributeId}" .
                " AND {$dAlias}.store_id = 0",
                []
            );
            $select->joinLeft(
                [$sAlias => $attributeTable],
                "{$sAlias}.{$productIdField} = {$entity} AND {$sAlias}.attribute_id = {$attributeId}" .
                " AND {$sAlias}.store_id = {$store}",
                []
            );
            $expression = $connection->getCheckSql(
                $connection->getIfNullSql("{$sAlias}.value_id", -1) . ' > 0',
                "{$sAlias}.value",
                "{$dAlias}.value"
            );
        }

        if ($condition !== null) {
            $select->where("{$expression}{$condition}");
        }

        return $expression;
    }

    /**
     * Add website data join to select
     * If add default store join also limitation of only has default store website
     * Joined table has aliases
     *  cw for website table,
     *  csg for store group table (joined by website default group)
     *  cs for store table (joined by website default store)
     *
     * @param \Magento\Framework\DB\Select $select the select object
     * @param bool $store add default store join
     * @param string|\Zend_Db_Expr $joinCondition the limitation for website_id
     * @return $this
     */
    protected function _addWebsiteJoinToSelect($select, $store = true, $joinCondition = null)
    {
        if ($joinCondition !== null) {
            $joinCondition = 'cw.website_id = ' . $joinCondition;
        }

        $select->join(['cw' => $this->getTable('store_website')], $joinCondition, []);

        if ($store) {
            $select->join(
                ['csg' => $this->getTable('store_group')],
                'csg.group_id = cw.default_group_id',
                []
            )->join(
                ['cs' => $this->getTable('store')],
                'cs.store_id = csg.default_store_id',
                []
            );
        }

        return $this;
    }

    /**
     * Add join for vendor_website table
     * Joined table has alias pw
     *
     * @param \Magento\Framework\DB\Select $select the select object
     * @param string|\Zend_Db_Expr $website the limitation of website_id
     * @param string|\Zend_Db_Expr $vendor the limitation of vendor_id
     * @return $this
     */
    protected function _addVendorWebsiteJoinToSelect($select, $website, $vendor)
    {
        $select->join(
            ['vw' => $this->getTable('omnyfy_vendor_profile')],
            "vw.vendor_id = {$vendor} AND vw.website_id = {$website}",
            []
        );

        return $this;
    }

    /**
     * @return \Magento\Framework\EntityManager\MetadataPool
     */
    protected function getMetadataPool()
    {
        if (null === $this->metadataPool) {
            $this->metadataPool = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Framework\EntityManager\MetadataPool');
        }
        return $this->metadataPool;
    }
}
 