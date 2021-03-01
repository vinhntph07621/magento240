<?php

namespace Omnyfy\Vendor\Model\Resource\Location;

class Config extends \Omnyfy\Core\Model\ResourceModel\Eav\AbstractConfig
{

    /**
     * Retrieve entity type id
     *
     * @return int
     */
    public function getEntityTypeId()
    {
        if ($this->_entityTypeId === null) {
            $this->_entityTypeId = (int)$this->_eavConfig->getEntityType(\Omnyfy\Vendor\Model\Location::ENTITY)
                ->getId();
        }
        return $this->_entityTypeId;
    }

    public function getAttributesUsedInListing()
    {
        $connection = $this->getConnection();
        $storeLabelExpr = $connection->getCheckSql('al.value IS NOT NULL', 'al.value', 'main_table.frontend_label');

        $select = $connection->select()->from(
            ['main_table' => $this->getTable('eav_attribute')]
        )->join(
            ['additional_table' => $this->getTable('omnyfy_vendor_eav_attribute')],
            'main_table.attribute_id = additional_table.attribute_id'
        )->joinLeft(
            ['al' => $this->getTable('eav_attribute_label')],
            'al.attribute_id = main_table.attribute_id AND al.store_id = ' . (int)$this->getStoreId(),
            ['store_label' => $storeLabelExpr]
        )->where(
            'main_table.entity_type_id = ?',
            $this->getEntityTypeId()
        )->where(
            'additional_table.used_in_listing = ?',
            1
        );
        return $connection->fetchAll($select);
    }
}
