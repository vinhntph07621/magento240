<?php

namespace Omnyfy\LayeredNavigation\Model\Layer\Filter;

abstract class AbstractFilter extends \Magento\Framework\DataObject implements FilterInterface
{

    const ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS = 1;

    /**
     * @var string
     */
    protected $_requestVar;

    /**
     * @var array
     */
    protected $_items;

    /**
     * @var \Omnyfy\LayeredNavigation\Model\AbstractLayer
     */
    protected $_layer;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Omnyfy\LayeredNavigation\Model\Layer\Filter\ItemFactory
     */
    protected $_filterItemFactory;

    /**
     * @var \Omnyfy\LayeredNavigation\Model\Layer\Filter\Item\DataBuilder
     */
    protected $_itemDataBuilder;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @param \Omnyfy\LayeredNavigation\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Omnyfy\LayeredNavigation\Model\Layer\Resolver $layerResolver
     * @param \Omnyfy\LayeredNavigation\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Framework\UrlInterface $url
     * @param array $data
     */
    public function __construct(
        \Omnyfy\LayeredNavigation\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\LayeredNavigation\Model\Layer\Resolver $layerResolver,
        \Omnyfy\LayeredNavigation\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\UrlInterface $url,
        array $data = []
    ) {
        $this->_filterItemFactory = $filterItemFactory;
        $this->_storeManager = $storeManager;
        $this->_layer = $layerResolver->get();
        $this->_itemDataBuilder = $itemDataBuilder;
        $this->_url = $url;

        parent::__construct($data);

        if ($this->hasAttributeModel()) {
            $this->_requestVar = $this->getAttributeModel()->getAttributeCode();
        }
    }

    /**
     * Set request variable name which is used for apply filter
     *
     * @param string $varName
     * @return $this
     */
    public function setRequestVar($varName)
    {
        $this->_requestVar = $varName;

        return $this;
    }

    /**
     * Get request variable name which is used for apply filter
     *
     * @return string
     */
    public function getRequestVar()
    {
        $tableAlias = $this->_getData('collection_table_alias');
        if (!empty($tableAlias) && $tableAlias != 'e') {
            return $tableAlias . '_' . $this->_requestVar;
        }

        return $this->_requestVar;
    }

    /**
     * Get filter value for reset current filter state
     *
     * @return mixed
     */
    public function getResetValue()
    {
        return null;
    }

    /**
     * Retrieve filter value for Clear All Items filter state
     *
     * @return mixed
     */
    public function getCleanValue()
    {
        return null;
    }

    /**
     * Apply filter to collection
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        return $this;
    }

    /**
     * Get fiter items count
     *
     * @return int
     */
    public function getItemsCount()
    {
        return count($this->getItems());
    }

    /**
     * Get all filter items
     *
     * @return array
     */
    public function getItems()
    {
        if ($this->_items === null) {
            $this->_initItems();
        }

        return $this->_items;
    }

    /**
     * Set all filter items
     *
     * @param array $items
     * @return $this
     */
    public function setItems(array $items)
    {
        $this->_items = $items;

        return $this;
    }

    /**
     * Get data array for building filter items
     *
     * Result array should have next structure:
     * array(
     *      $index => array(
     *          'label' => $label,
     *          'value' => $value,
     *          'count' => $count
     *      )
     * )
     *
     * @return array
     */
    protected function _getItemsData()
    {
        return [];
    }

    /**
     * Initialize filter items
     *
     * @return $this
     */
    protected function _initItems()
    {
        $data = $this->_getItemsData();
        $items = [];
        foreach ($data as $itemData) {
            $items[] = $this->_createItem($itemData['label'], $itemData['value'], $itemData['count']);
        }
        $this->_items = $items;

        return $this;
    }

    /**
     * Retrieve layer object
     *
     * @return \Omnyfy\LayeredNavigation\Model\AbstractLayer
     */
    public function getLayer()
    {
        $layer = $this->_getData('layer');
        if ($layer === null) {
            $layer = $this->_layer;
            $this->setData('layer', $layer);
        }

        return $layer;
    }

    /**
     * Create filter item object
     *
     * @param string $label
     * @param mixed $value
     * @param int $count
     * @return \Omnyfy\LayeredNavigation\Model\Layer\Filter\Item
     */
    protected function _createItem($label, $value, $count = 0)
    {
        return $this->_filterItemFactory->create()
            ->setFilter($this)
            ->setLabel($label)
            ->setValue($value)
            ->setCount($count);
    }

    /**
     * Get all product ids from from collection with applied filters
     *
     * @return array
     */
    protected function _getFilterEntityIds()
    {
        return $this->getLayer()->getProductCollection()->getAllIdsCache();
    }

    /**
     * Get product collection select object with applied filters
     *
     * @return \Magento\Framework\DB\Select
     */
    protected function _getBaseCollectionSql()
    {
        return $this->getLayer()->getProductCollection()->getSelect();
    }

    /**
     * Set attribute model to filter
     *
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return $this
     */
    public function setAttributeModel($attribute)
    {
        $this->setRequestVar($attribute->getAttributeCode());
        $this->setData('attribute_model', $attribute);

        return $this;
    }

    /**
     * Get attribute model associated with filter
     *
     * @return \Magento\Eav\Model\Entity\Attribute
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAttributeModel()
    {
        $attribute = $this->getData('attribute_model');
        if ($attribute === null) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The attribute model is not defined.'));
        }

        return $attribute;
    }

    /**
     * Get filter text label
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getName()
    {
        return $this->getAttributeModel()->getStoreLabel();
    }

    /**
     * Retrieve current store id scope
     *
     * @return int
     */
    public function getStoreId()
    {
        $storeId = $this->_getData('store_id');
        if ($storeId === null) {
            $storeId = $this->_storeManager->getStore()->getId();
        }

        return $storeId;
    }

    /**
     * Set store id scope
     *
     * @param int $storeId
     * @return $this
     */
    public function setStoreId($storeId)
    {
        return $this->setData('store_id', $storeId);
    }

    /**
     * Retrieve Website ID scope
     *
     * @return int
     */
    public function getWebsiteId()
    {
        $websiteId = $this->_getData('website_id');
        if ($websiteId === null) {
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();
        }
        return $websiteId;
    }

    /**
     * Set Website ID scope
     *
     * @param int $websiteId
     * @return $this
     */
    public function setWebsiteId($websiteId)
    {
        return $this->setData('website_id', $websiteId);
    }

    /**
     * Clear current element link text, for example 'Clear Price'
     *
     * @return false|string
     */
    public function getClearLinkText()
    {
        return false;
    }

    /**
     * Get option text from frontend model by option id
     *
     * @param int $optionId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return string|bool
     */
    protected function getOptionText($optionId)
    {
        return $this->getAttributeModel()->getFrontend()->getOption($optionId);
    }

    /**
     * Check whether specified attribute can be used in LN
     *
     * @param \Magento\Eav\Model\Entity\Attribute $attribute
     * @return int
     */
    protected function getAttributeIsFilterable($attribute)
    {
        return $attribute->getIsFilterable();
    }

    /**
     * Checks whether the option reduces the number of results
     *
     * @param int $optionCount Count of search results with this option
     * @param int $totalSize Current search results count
     * @return bool
     */
    protected function isOptionReducesResults($optionCount, $totalSize)
    {
        return $optionCount < $totalSize;
    }

    /**
     * Get clear link url
     *
     * @return mixed
     */
    public function getClearLinkUrl()
    {
        $clearLinkText = $this->getClearLinkText();
        if (!$clearLinkText) {
            return false;
        }

        return $this->_url->getUrl('*/*/*', [
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => [$this->getRequestVar() => null],
            '_escape' => true,
        ]);
    }

}
