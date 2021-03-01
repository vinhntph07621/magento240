<?php
/**
 * Project: Vendor.
 * User: jing
 * Date: 25/1/18
 * Time: 12:17 PM
 */

namespace Omnyfy\Vendor\Model\Resource\Inventory\Grid;

class Collection extends \Omnyfy\Vendor\Model\Resource\Inventory\Collection
    implements \Magento\Framework\Api\Search\SearchResultInterface
{
    protected $entityAttribute;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Entity\Attribute $entityAttribute,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        $this->entityAttribute = $entityAttribute;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    public function getAggregations()
    {
    }

    public function setAggregations($aggregations)
    {
    }

    public function _initSelect()
    {
        parent::_initSelect();

        $productName = $this->entityAttribute->loadByCode('catalog_product', 'name');

        $this->getSelect()->join(
            ['product' => $this->getTable('catalog_product_entity')],
            'main_table.product_id = product.entity_id',
            ['sku' => 'product.sku', 'type_id' => 'product.type_id']
        )
        ->joinLeft(
            ['pv' => $this->getTable('catalog_product_entity_varchar')],
            'pv.entity_id=product.entity_id AND pv.store_id=0 AND pv.attribute_id='. $productName->getId(),
            ['product_name' => 'pv.value']
        )
        ;
        return $this;
    }

    public function getSearchCriteria()
    {
        return null;
    }

    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria)
    {
        return $this;
    }

    public function getTotalCount()
    {
        return $this->getSize();
    }

    public function setTotalCount($totalCount)
    {
        return $this;
    }

    public function setItems(array $items=null)
    {
        return $this;
    }

    protected function _getItemId(\Magento\Framework\DataObject $item)
    {

    }
}