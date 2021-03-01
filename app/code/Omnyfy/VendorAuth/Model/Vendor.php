<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 24/03/2020
 * Time: 5:09 PM
 */

namespace Omnyfy\VendorAuth\Model;


class Vendor extends \Magento\Eav\Model\Entity\AbstractEntity
{
    const TABLE_PREFIX = "omnyfy_vendor_vendor_";

    public function getVendorEntityFromType($type, $vendorId){
        //try {

            $table = 'omnyfy_vendor_vendor_' . $type;
            $column = $type . '_id';

            return $this->getVendorEntityFromTable($table, $column, $vendorId);
        //}catch (\Exception $exception){

        //}
    }

    public function getVendorEntityFromTable($table, $column, $vendorId){
        $conn = $this->getConnection();
        if (!$conn->isTableExists($table))
            return null;

        $table = $conn->getTableName($table);

        $select = $conn->select()->from(
            $table,
            [$column]
        )->where("vendor_id = ?", $vendorId );

        return $conn->fetchCol($select);
    }
}