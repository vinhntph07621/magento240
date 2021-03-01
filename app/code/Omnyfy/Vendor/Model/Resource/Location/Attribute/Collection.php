<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 8/9/17
 * Time: 10:50 AM
 */
namespace Omnyfy\Vendor\Model\Resource\Location\Attribute;

class Collection extends \Magento\Eav\Model\ResourceModel\Entity\Attribute\Collection
{
    protected $_eavEntityFactory;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        $this->_eavEntityFactory = $eavEntityFactory;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $eavConfig, $connection, $resource);
    }

    protected function _construct()
    {
        $this->_init('Omnyfy\Vendor\Model\Resource\Eav\Attribute',
            'Magento\Eav\Model\ResourceModel\Entity\Attribute');
    }

    /**
     * Initialize select object
     *
     * @return $this
     */
    protected function _initSelect()
    {
        $entityTypeId = (int)$this->_eavEntityFactory->create()->setType(
            \Omnyfy\Vendor\Model\Location::ENTITY
        )->getTypeId();
        $columns = $this->getConnection()->describeTable($this->getResource()->getMainTable());
        unset($columns['attribute_id']);
        $retColumns = [];
        foreach ($columns as $labelColumn => $columnData) {
            $retColumns[$labelColumn] = $labelColumn;
            if ($columnData['DATA_TYPE'] == \Magento\Framework\DB\Ddl\Table::TYPE_TEXT) {
                $retColumns[$labelColumn] = 'main_table.' . $labelColumn;
            }
        }
        $this->getSelect()->from(
            ['main_table' => $this->getResource()->getMainTable()],
            $retColumns
        )->join(
            ['additional_table' => $this->getTable('omnyfy_vendor_eav_attribute')],
            'additional_table.attribute_id = main_table.attribute_id'
        )->where(
            'main_table.entity_type_id = ?',
            $entityTypeId
        );
        return $this;
    }

    /**
     * Specify attribute entity type filter.
     * Entity type is defined.
     *
     * @param  int $typeId
     * @return $this
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setEntityTypeFilter($typeId)
    {
        return $this;
    }

    /**
     * Return array of fields to load attribute values
     *
     * @return string[]
     */
    protected function _getLoadDataFields()
    {
        $fields = array_merge(
            parent::_getLoadDataFields(),
            [
                'additional_table.is_global',
                //'additional_table.is_html_allowed_on_front',
                //'additional_table.is_wysiwyg_enabled'
            ]
        );

        return $fields;
    }

    /**
     * Specify "is_filterable" filter
     *
     * @return $this
     */
    public function addIsFilterableFilter()
    {
        return $this->addFieldToFilter('additional_table.is_filterable', ['gt' => 0]);
    }

    /**
     * Add filterable in search filter
     *
     * @return $this
     */
    public function addIsFilterableInSearchFilter()
    {
        return $this->addFieldToFilter('additional_table.is_filterable_in_search', ['gt' => 0]);
    }

    /**
     * Specify filter by "is_visible" field
     *
     * @return $this
     */
    public function addVisibleFilter()
    {
        return $this->addFieldToFilter('additional_table.is_visible', 1);
    }

    /**
     * Specify "is_searchable" filter
     *
     * @return $this
     */
    public function addIsSearchableFilter()
    {
        return $this->addFieldToFilter('additional_table.is_searchable', 1);
    }

    /**
     * Specify filter for attributes that have to be indexed
     *
     * @param bool $addRequiredCodes
     * @return $this
     */
    public function addToIndexFilter($addRequiredCodes = false)
    {
        $conditions = [
            'additional_table.is_searchable = 1',
            //'additional_table.is_visible_in_advanced_search = 1',
            'additional_table.is_filterable > 0',
            //'additional_table.is_filterable_in_search = 1',
            //'additional_table.used_for_sort_by = 1',
        ];

        if ($addRequiredCodes) {
            $conditions[] = $this->getConnection()->quoteInto(
                'main_table.attribute_code IN (?)',
                ['status']
            );
        }

        $this->getSelect()->where(sprintf('(%s)', implode(' OR ', $conditions)));

        return $this;
    }

    /**
     * Specify filter for attributes used in quick search
     *
     * @return $this
     */
    public function addSearchableAttributeFilter()
    {
        $this->getSelect()->where(
            'additional_table.is_searchable = 1 OR ' . $this->getConnection()->quoteInto(
                'main_table.attribute_code IN (?)',
                ['status', 'visibility']
            )
        );

        return $this;
    }

    /**
     * Add is used in grid filter
     *
     * @return $this
     */
    public function addIsUsedInGridFilter()
    {
        return $this->addFieldToFilter('additional_table.is_used_in_grid', 1);
    }
}