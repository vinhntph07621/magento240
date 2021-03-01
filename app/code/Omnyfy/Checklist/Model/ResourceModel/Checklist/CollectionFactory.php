<?php
namespace Omnyfy\Checklist\Model\ResourceModel\Checklist;

class CollectionFactory
{

    protected $_objectManager = null;


    protected $_instanceName = null;


    public function __construct(\Magento\Framework\ObjectManagerInterface $objectManager, $instanceName = '\\Omnyfy\\Checklist\\Model\\ResourceModel\\Checklist\\Collection')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }
}
