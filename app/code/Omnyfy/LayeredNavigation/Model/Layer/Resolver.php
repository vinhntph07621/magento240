<?php

namespace Omnyfy\LayeredNavigation\Model\Layer;

class Resolver
{

    /**
     * @var array
     */
    protected $_layersPool;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Omnyfy\LayeredNavigation\Model\AbstractLayer
     */
    protected $_layer = null;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $layersPool
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $layersPool
    ) {
        $this->_objectManager = $objectManager;
        $this->_layersPool = $layersPool;
    }

    /**
     * Create Catalog Layer by specified type
     *
     * @param string $layerType
     * @return void
     */
    public function create($layerType)
    {
        if (isset($this->_layer)) {
            throw new \RuntimeException('Layer has been already created');
        }
        if (!isset($this->_layersPool[$layerType])) {
            throw new \InvalidArgumentException($layerType . ' does not belong to any registered layer');
        }
        $this->_layer = $this->_objectManager->create($this->_layersPool[$layerType]);

        return $this->_layer;
    }

    /**
     * Get current layer
     *
     * @return \Omnyfy\LayeredNavigation\Model\AbstractLayer
     */
    public function get()
    {
        if (!isset($this->_layer)) {
            $this->_layer = $this->_objectManager->create(reset($this->_layersPool));
        }

        return $this->_layer;
    }

}
