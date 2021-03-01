<?php

namespace Omnyfy\LayeredNavigation\Block;

class Navigation extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Omnyfy\LayeredNavigation\Model\AbstractLayer
     */
    protected $_layer;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Omnyfy\LayeredNavigation\Model\Layer\Resolver $layerResolver
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Omnyfy\LayeredNavigation\Model\Layer\Resolver $layerResolver,
        array $data = []
    ) {
        $this->_layer = $layerResolver->get();

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Apply layer
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->renderer = $this->getChildBlock('renderer');

        foreach ($this->getLayer()->getFilters() as $filter) {
            $filter->apply($this->getRequest());
        }

        $this->getLayer()->apply();

        return parent::_prepareLayout();
    }

    /**
     * Get layer
     *
     * @return \Omnyfy\LayeredNavigation\Model\AbstractLayer
     */
    public function getLayer()
    {
        return $this->_layer;
    }

    /**
     * Get layer filters
     *
     * @return \Omnyfy\LayeredNavigation\Model\Layer\Filter\AbstractFilter[]
     */
    public function getFilters()
    {
        return $this->getLayer()->getFilters();
    }

    /**
     * Retrieve Clear Filters URL
     *
     * @return string
     */
    public function getClearUrl()
    {
        $filterState = [];
        foreach ($this->getLayer()->getState()->getFilters() as $item) {
            $filterState[$item->getFilter()->getRequestVar()] = $item->getFilter()->getCleanValue();
        }

        return $this->_urlBuilder->getUrl('*/*/*', [
            '_current' => true,
            '_use_rewrite' => true,
            '_query' => $filterState,
            '_escape' => true
        ]);
    }

}
