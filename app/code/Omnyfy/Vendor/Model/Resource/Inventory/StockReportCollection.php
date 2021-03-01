<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 29/1/18
 * Time: 5:35 PM
 */
namespace Omnyfy\Vendor\Model\Resource\Inventory;

class StockReportCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'inventory_id';

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection
     */
    protected $_entityAttributeCollection;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute
     */
    protected $_entityAttribute;

    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection
     */
    protected $_attributeOptionCollection;


    public function __construct(
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection $entityAttributeCollection,
        \Magento\Eav\Model\Entity\Attribute $entityAttribute,
        \Magento\Eav\Model\ResourceModel\Entity\Attribute\Option\Collection $attributeOptionCollection,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        $this->_entityAttributeCollection = $entityAttributeCollection;
        $this->_entityAttribute = $entityAttribute;
        $this->_attributeOptionCollection = $attributeOptionCollection;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    protected function _construct()
    {
        $this->_init('Omnyfy\Vendor\Model\Inventory', 'Omnyfy\Vendor\Model\Resource\Inventory');
    }

    public function _initSelect(){
        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['le' => $this->getTable('omnyfy_vendor_location_entity')],
            'main_table.location_id = le.entity_id',
            [
                'location_name'  => 'le.location_name'
            ]
        );

        $this->getSelect()->joinLeft(
            ['ve' => $this->getTable('omnyfy_vendor_vendor_entity')],
            'le.vendor_id = ve.entity_id',
            [
                'vendor_id' => 've.entity_id',
                'vendor_name'  => 've.name',
                'vendor_email' => 've.email',
                'vendor_status' => 've.status',
            ]
        );

        $this->getSelect()->joinLeft(
            ['pe' => $this->getTable('catalog_product_entity')],
            'main_table.product_id = pe.entity_id',
            [
                'sku'  => 'pe.sku'
            ]
        );


        $productName = $this->_entityAttribute->loadByCode('catalog_product', 'name');

        $this->getSelect()->joinLeft(
            ['pv' => $this->getTable('catalog_product_entity_varchar')],
            'pv.entity_id=pe.entity_id AND pv.store_id=0 AND pv.attribute_id='. $productName->getId(),
            ['product_name' => 'pv.value']
        );

        $subquery = new \Zend_Db_Expr('(SELECT count(DISTINCT order_id) from sales_order_item AS t WHERE t.sku =pe.sku AND t.location_id = main_table.location_id)');
        $this->getSelect()->columns(
            [
                "num_of_orders" => $subquery
            ]
        );

        return $this;
    }
}