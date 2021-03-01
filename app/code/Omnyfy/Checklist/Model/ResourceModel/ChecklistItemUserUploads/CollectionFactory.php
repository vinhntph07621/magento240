<?php
namespace Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserUploads;

class CollectionFactory
{

    protected $_objectManager = null;
    protected $_instanceName = null;
    protected $_resource;
    protected $_connection;


    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $resources,
        $instanceName = '\\Omnyfy\\Checklist\\Model\\ResourceModel\\ChecklistItemUserUploads\\Collection')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
        $this->_resource      = $resources;
        $this->_connection    = $resources->getConnection();
    }

    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }

    public function saveOptions(array $data = array()) {
        $tableReadingList = $this->_resource->getTableName('omnyfy_checklist_checklistitemuseruploads');
        $columns = implode(", ",array_keys($data));
        $escaped_values = array_values($data);
        $values  = "'".implode("', '", $escaped_values)."'";
        $sql = "INSERT INTO ".$tableReadingList."($columns) VALUES ($values)";
        return $this->_connection->query($sql);
    }
}
