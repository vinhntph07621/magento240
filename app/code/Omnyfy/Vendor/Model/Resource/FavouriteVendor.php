<?php
/**
 * Project: Multi Vendor M2.
 * User: seth
 * Date: 6/9/19
 * Time: 11:36 AM
 */

namespace Omnyfy\Vendor\Model\Resource;

class FavouriteVendor extends \Omnyfy\Core\Model\ResourceModel\AbstractDbModel
{
    protected function _construct()
    {
        $this->_init('omnyfy_vendor_customer_favourites', 'id');
    }

    public function getUpdateFields()
    {
        return [
            'customer_id',
            'vendor_id'
        ];
    }

    /** START Favourite Vendors Functionality */
    public function getFavouriteBrokersIdByCustomerId($customerId) {
        $conn = $this->getConnection();

        $table = $this->getMainTable();
        $select = $conn->select()->from(
            $table,
            ['vendor_id']
        )
            ->where('customer_id=?', $customerId)
        ;

        return $conn->fetchOne($select);
    }

    public function saveFavouriteBrokersId($customerId, $vendorId) {
        if (empty($customerId) || empty($vendorId)) {
            return;
        }

        $data = [
            'customer_id' => $customerId,
            'vendor_id' => $vendorId
        ];

        $this->bulkSave([$data]);
    }

    public function removeFavouriteBrokersId($customerId, $vendorId) {
        if (empty($customerId) || empty($vendorId)) {
            return;
        }

        $conn = $this->getConnection();

        $table = $this->getMainTable();
        $conn->delete($table,
            [
                'customer_id IN (?)' => $customerId,
                'vendor_id IN (?)' => $vendorId
            ]
        );
    }
    /** END Favourite Vendors Functionality */
}