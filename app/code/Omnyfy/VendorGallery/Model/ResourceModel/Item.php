<?php
namespace Omnyfy\VendorGallery\Model\ResourceModel;

class Item extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('omnyfy_vendor_gallery_item', 'entity_id');
    }

    /**
     * Delete Items
     *
     * @param $ids
     */
    public function deleteItemByIds($ids)
    {
        $conn = $this->getConnection();
        $conn->delete(
            $this->getTable('omnyfy_vendor_gallery_item'),
            ['entity_id in (?)' => $ids]
        );
    }

    /**
     * Update Items
     *
     * @param $updateData
     */
    public function updateItemsData($updateData)
    {
        $conn = $this->getConnection();
        foreach ($updateData as $id => $data) {
            $conn->update(
                $this->getTable('omnyfy_vendor_gallery_item'),
                [
                    'position' => $data['position'],
                    'is_thumbnail' => $data['is_thumbnail'],
                    'status' => $data['status'],
                    'caption' => $data['caption']
                ],
                ['entity_id = ?' => $id]
            );
        }
    }
}
