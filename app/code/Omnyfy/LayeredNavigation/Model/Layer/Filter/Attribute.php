<?php

namespace Omnyfy\LayeredNavigation\Model\Layer\Filter;

class Attribute extends \Omnyfy\LayeredNavigation\Model\Layer\Filter\AbstractFilter
{

    /**
     * @var \Omnyfy\LayeredNavigation\Model\ResourceModel\Layer\Filter\Attribute
     */
    protected $_resource;

    /**
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * @var \Magento\Framework\Filter\StripTags
     */
    protected $tagFilter;

    /**
     * @param \Omnyfy\LayeredNavigation\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Omnyfy\LayeredNavigation\Model\Layer\Resolver $layerResolver
     * @param \Omnyfy\LayeredNavigation\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Framework\UrlInterface $url
     * @param \Omnyfy\LayeredNavigation\Model\ResourceModel\Layer\Filter\AttributeFactory $filterAttributeFactory
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Framework\Filter\StripTags $tagFilter
     * @param array $data
     */
    public function __construct(
        \Omnyfy\LayeredNavigation\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\LayeredNavigation\Model\Layer\Resolver $layerResolver,
        \Omnyfy\LayeredNavigation\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        \Magento\Framework\UrlInterface $url,
        \Omnyfy\LayeredNavigation\Model\ResourceModel\Layer\Filter\AttributeFactory $filterAttributeFactory,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Framework\Filter\StripTags $tagFilter,
        array $data = []
    ) {
        $this->_resource = $filterAttributeFactory->create();
        $this->string = $string;
        $this->_requestVar = 'attribute';
        $this->tagFilter = $tagFilter;

        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layerResolver,
            $itemDataBuilder,
            $url,
            $data
        );
    }

    /**
     * Retrieve resource instance
     *
     * @return \Omnyfy\LayeredNavigation\Model\ResourceModel\Layer\Filter\Attribute
     */
    protected function _getResource()
    {
        return $this->_resource;
    }

    /**
     * Apply attribute option filter to product collection
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        $filter = $request->getParam($this->getRequestVar());
        if (is_array($filter)) {
            return $this;
        }

        $text = $this->getOptionText($filter);
        if (!is_null($filter)) {
            $this->_getResource()->applyFilterToCollection($this, $filter);
            $this->getLayer()->getState()->addFilter($this->_createItem($text, $filter));
        }

        return $this;
    }

    /**
     * Get data array for building attribute filter items
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     */
    protected function _getItemsData()
    {
        $attribute = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $options = $attribute->getFrontend()->getSelectOptions();
        $optionsCount = $this->_getResource()->getCount($this);
        foreach ($options as $option) {
            if (is_array($option['value'])) {
                continue;
            }
            if ($this->string->strlen($option['value'])) {
                // Check filter type
                if ($this->getAttributeIsFilterable($attribute) == self::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS) {
                    if (!empty($optionsCount[$option['value']])) {
                        $this->_itemDataBuilder->addItemData(
                            $this->tagFilter->filter($option['label']),
                            $option['value'],
                            $optionsCount[$option['value']]
                        );
                    }
                } else {
                    $this->_itemDataBuilder->addItemData(
                        $this->tagFilter->filter($option['label']),
                        $option['value'],
                        isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0
                    );
                }
            }
        }

        return $this->_itemDataBuilder->build();
    }

    public function getClearLinkText()
    {
        return 'Clear filter';
    }

}

