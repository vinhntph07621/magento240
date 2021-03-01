<?php
namespace Omnyfy\Checklist\Model\ResourceModel\ChecklistItemUserOptions;

class CollectionFactory
{

    protected $_objectManager = null;
    protected $_instanceName = null;
    protected $_resource;
    protected $_connection;
    protected $_table;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\ResourceConnection $resources,
        $instanceName = '\\Omnyfy\\Checklist\\Model\\ResourceModel\\ChecklistItemUserOptions\\Collection')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName  = $instanceName;
        $this->_resource      = $resources;
        $this->_connection    = $resources->getConnection();
        $this->_table         = $this->_resource->getTableName('omnyfy_checklist_checklistitemuseroptions');
    }

    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }

    public function saveOptions(array $data = array()) {
        $columns = implode(", ",array_keys($data));
        $escaped_values = array_values($data);
        $values  = implode(", ", $escaped_values);
        $sql = "INSERT INTO ".$this->_table."($columns) VALUES ($values)";
        $this->_connection->query($sql);
        return true;
    }

    public function deleteOption($id) {
        try {
            $sql = "DELETE FROM " . $this->_table . " WHERE checklistitemuseroptions_id =" . $id . ";";
            $this->_connection->query($sql);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
