<?php

namespace Omnyfy\LayeredNavigation\Block\Navigation;

class State extends \Magento\Framework\View\Element\Template
{

    /**
     * @var string
     */
    protected $_template = 'navigation/state.phtml';

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
     * Retrieve active filters
     *
     * @return array
     */
    public function getActiveFilters()
    {
        $filters = $this->getLayer()->getState()->getFilters();
        if (!is_array($filters)) {
            $filters = [];
        }

        return $filters;
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

}
