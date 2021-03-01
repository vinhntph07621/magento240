<?php

namespace Omnyfy\LayeredNavigation\Model;

abstract class AbstractLayer extends \Magento\Framework\DataObject
{

    /**
     * @var string[]
     */
    protected $_filterTypes = [
        'attribute' => 'Omnyfy\LayeredNavigation\Model\Layer\Filter\Attribute',
    ];

    /**
     * @var string
     */
    protected $_eavAttributeModel = 'Magento\Eav\Model\Entity\Attribute';

    /**
     * @var array
     */
    protected $_entityTypes = [];

    /**
     * @var array
     */
    protected $_attributes;

    /**
     * @var \Magento\Eav\Model\Entity\TypeFactory
     */
    protected $_entityTypeFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var array
     */
    protected $_filters;

    /**
     * @var \Omnyfy\LayeredNavigation\Model\Layer\StateFactory
     */
    protected $_stateFactory;

    /**
     * @var \Omnyfy\LayeredNavigation\Model\Layer\State
     */
    protected $_state;

    /**
     * @param \Magento\Eav\Model\Entity\TypeFactory $entityTypeFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Omnyfy\LayeredNavigation\Model\Layer\StateFactory $stateFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Eav\Model\Entity\TypeFactory $entityTypeFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Omnyfy\LayeredNavigation\Model\Layer\StateFactory $stateFactory,
        array $data = []
    ) {
        $this->_entityTypeFactory = $entityTypeFactory;
        $this->_objectManager = $objectManager;
        $this->_stateFactory = $stateFactory;

        parent::__construct($data);
    }

    /**
     * Get collection
     *
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    abstract public function getCollection();

    /**
     * Apply layer
     *
     * @return $this
     */
    public function apply()
    {
        return $this;
    }

    /**
     * Get layer state
     *
     * @return \Omnyfy\LayeredNavigation\Model\Layer\State
     */
    public function getState()
    {
        if (!$this->_state) {
            $this->_state = $this->_stateFactory->create();
        }

        return $this->_state;
    }

    /**
     * Get filterable attributes
     *
     * @return array
     */
    public function getFilterableAttributes()
    {
        if (!$this->_attributes) {
            foreach ($this->_entityTypes as $table => $type) {
                $entityType = $this->_entityTypeFactory->create()->loadByCode($type['code']);
                $collection = $entityType->getAttributeCollection();
                $collection->setItemObjectClass($type['attribute_model']);
                $collection->addFieldToFilter('additional_table.is_filterable', ['gt' => 0]);

                foreach ($collection as $attribute) {
                    $this->_attributes[$table][] = $attribute;
                }
            }
        }

        return $this->_attributes;
    }

    /**
     * Retrieve list of filters
     *
     * @return Omnyfy\LayeredNavigation\Model\Layer\Filter\AbstractFilter[]
     */
    public function getFilters()
    {
        if (!$this->_filters) {

            $filterableAttributes = $this->getFilterableAttributes();

            // check if filterableAttributes is empty 
            if(!empty($filterableAttributes)){
                foreach ($this->getFilterableAttributes() as $collectionTableAlias => $attributes) {
                    foreach ($attributes as $attribute) {
                        $this->_filters[] = $this->_createAttributeFilter($attribute, $collectionTableAlias);
                    }
                }
            }
        }

        return $this->_filters;
    }

    /**
     * Create filter
     *
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @param string $collectionTableAlias
     * @return \Omnyfy\LayeredNavigation\Model\Layer\Filter\AbstractFilter
     */
    protected function _createAttributeFilter(
        \Magento\Eav\Model\Entity\Attribute $attribute,
        $collectionTableAlias
    ) {
        $filterClassName = $this->_getAttributeFilterClass($attribute);

        $filter = $this->_objectManager->create(
            $filterClassName,
            ['data' => ['attribute_model' => $attribute, 'collection_table_alias' => $collectionTableAlias], 'layer' => $this]
        );

        return $filter;
    }

    /**
     * Get Attribute Filter Class Name
     *
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return string
     */
    protected function _getAttributeFilterClass(\Magento\Eav\Model\Entity\Attribute $attribute)
    {
        $filterClassName = $this->_filterTypes['attribute'];

        if (isset($this->_filterTypes[$attribute->getAttributeCode()])) {
            $filterClassName = $this->_filterTypes[$attribute->getAttributeCode()];
        }

        return $filterClassName;
    }

}
