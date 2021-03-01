<?php

namespace Omnyfy\Core\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

abstract class AbstractDbModel extends AbstractDb
{

    /**
     * Get update fields
     *
     * @return array
     */
    abstract protected function getUpdateFields();

    /**
     * Bulk save
     *
     * @param array $data
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function bulkSave($data)
    {
        if (empty($data)) {
            return;
        }

        $conn = $this->getConnection();

        $conn->insertOnDuplicate(
            $this->getMainTable(),
            $data,
            $this->getUpdateFields()
        );
    }

    /**
     * Remove
     *
     * @param array $data conditions to join with AND
     * @param string|null $table
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    public function remove($data, $table = null)
    {
        if (empty($data)) {
            return;
        }

        $conn = $this->getConnection();

        $condition = [];
        foreach($data as $key => $values) {
            if (is_string($key) && !is_numeric($key)) {
                if (is_array($values)) {
                    $condition[] = $conn->quoteInto($key. ' IN (?)', $values);
                }
                else{
                    $condition[] = $conn->quoteInto($key. '=?', $values);
                }
            }
        }

        if (empty($condition)) {
            return;
        }

        $table = empty($table) ? $this->getMainTable() : $table;
        $conn->delete($table, $condition);
    }

    /**
     * @param array $conditions Conditions to join with OR
     * @param string|null $table
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function removeOr($conditions, $table = null)
    {
        if (empty($data)) {
            return;
        }

        $conn = $this->getConnection();

        $condArray = [];
        foreach($conditions as $condition) {
            if (!is_array($condition)) {
                continue;
            }
            $andArray = [];
            foreach($condition as $key => $value) {
                if (is_numeric($key)) {
                    continue;
                }
                if (is_array($value)) {
                    $andArray[] = '(' . $conn->quoteInto($key . ' IN (?)', $value) . ')';
                }
                else {
                    $andArray[] = '(' . $conn->quoteInto($key . '=?', $value) . ')';
                }
            }
            if (!empty($andArray)) {
                $condArray[] = '(' . implode(' AND ', $andArray) . ')';
            }
        }

        if (empty($condArray)) {
            return;
        }

        $where = implode(' OR ', $condArray);
        $table = empty($table) ? $this->getMainTable() : $table;
        $conn->delete($table, $where);
    }

    /**
     *
     * @param string|\Zend_Db_Expr $where Condition to remove
     * @param string|null $table
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function removeAs($where, $table = null)
    {
        if (empty($where) || (!is_array($where) && !($where instanceof \Zend_Db_Expr) ) ) {
            return;
        }

        $conn = $this->getConnection();

        $table = empty($table) ? $this->getMainTable() : $conn->getTableName($table);
        $conn->delete($table, $where);
    }

    /**
     * Save to table
     *
     * @param string $table
     * @param array $data
     * @param array $updateColumns
     * @return void
     */
    protected function saveToTable($table, $data, $updateColumns = [])
    {
        if (empty($table) || empty($data)) {
            return;
        }

        $conn = $this->getConnection();

        $tableName = $this->getTable($table);

        if (empty($conn) || empty($tableName)) {
            return;
        }

        $conn->insertOnDuplicate($tableName, $data, $updateColumns);
    }

    public function updateById($field, $value, $id)
    {
        if (empty($field) || empty($id)) {
            return;
        }

        $conn = $this->getConnection();

        $tableName = $this->getMainTable();

        if (empty($conn) || empty($tableName)) {
            return;
        }

        $conn->update($tableName, [$field => $value], [$this->getIdFieldName().'=?' => $id]);
    }
}
