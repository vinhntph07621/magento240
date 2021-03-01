<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 6/6/17
 * Time: 11:09 AM
 */

namespace Omnyfy\Vendor\Model\Resource;

class Profile extends \Omnyfy\Core\Model\ResourceModel\AbstractDbModel
{
    protected function _construct()
    {
        $this->_init('omnyfy_vendor_profile', 'profile_id');
    }

    protected function getUpdateFields()
    {
        return [
            'updates',
        ];
    }

    public function getProfileIdsByVendorId($vendorId, $userId=null)
    {
        $conn = $this->getConnection();

        $table = $this->getMainTable();
        $select = $conn->select()->from(
            $table,
            ['profile_id', 'website_id']
        )->where(
            "vendor_id = ?",
            $vendorId
        )
        ;

        if (!empty($userId)) {
            $profileUserTable = $conn->getTableName('omnyfy_vendor_profile_admin_user');
            $select->join(
                ["pu" => $profileUserTable],
                "pu.profile_id=main_table.profile_id",
                []
            )->where(
                "pu.admin_user_id = ?",
                $userId
            )
            ;
        }

        $raw = $conn->fetchAll($select);
        $result = [];
        foreach($raw as $item) {
            $result[$item['website_id']] = $item['profile_id'];
        }
        return $result;
    }

    public function getProfileIdsByLocationId($locationId)
    {
        $conn = $this->getConnection();
        $table = $conn->getTableName('omnyfy_vendor_profile_location');

        $select = $conn->select()->from($table, ['profile_id'])
            ->where("location_id=?", $locationId)
        ;

        $result = $conn->fetchCol($select);
        return empty($result) ? [] : $result;
    }

    public function saveLocationRelation($data)
    {
        $this->saveToTable('omnyfy_vendor_profile_location', $data);
    }

    public function removeLocationRelation($conditions)
    {
        $this->remove($conditions, 'omnyfy_vendor_profile_location');
    }
}