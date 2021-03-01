<?php
namespace Omnyfy\Checklist\Model\ResourceModel\ChecklistItemOptions;

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
        $instanceName = '\\Omnyfy\\Checklist\\Model\\ResourceModel\\ChecklistItemOptions\\Collection')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
        $this->_resource     = $resources;
        $this->_connection   = $resources->getConnection();
        $this->_table        = $this->_resource->getTableName('omnyfy_checklist_checklistitemoptions');
    }

    public function create(array $data = array())
    {
        return $this->_objectManager->create($this->_instanceName, $data);
    }

    public function deleteOption($id) {
        try {
            $sql = "DELETE FROM " . $this->_table . " WHERE checklistitemoptions_id =" . $id . ";";
            $this->_connection->query($sql);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function deleteAll($itemId) {
        try {
            $sql = "DELETE FROM " . $this->_table . " WHERE item_id =" . $itemId . ";";
            $this->_connection->query($sql);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
