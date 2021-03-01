<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-26
 * Time: 16:42
 */
namespace Omnyfy\Vendor\Model\Indexer\Vendor\Flat\Action;

class Eraser
{

    /**
     * @var \Omnyfy\Vendor\Helper\Vendor\Flat\Indexer
     */
    protected $vendorIndexerHelper;

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Omnyfy\Vendor\Helper\Vendor\Flat\Indexer $indexHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Omnyfy\Vendor\Helper\Vendor\Flat\Indexer $indexHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->vendorIndexerHelper = $indexHelper;
        $this->connection = $resource->getConnection();
        $this->storeManager = $storeManager;
    }

    /**
     * Remove vendors from flat that are not exist
     *
     * @param array $ids
     * @param int $storeId
     * @return void
     */
    public function removeDeletedLocations(array &$ids, $storeId)
    {
        $select = $this->connection->select()->from(
            $this->vendorIndexerHelper->getTable('omnyfy_vendor_vendor_entity')
        )->where(
            'entity_id IN(?)',
            $ids
        );
        $result = $this->connection->query($select);

        $existentLocations = [];
        foreach ($result->fetchAll() as $location) {
            $existentLocations[] = $location['entity_id'];
        }

        $locationsToDelete = array_diff($ids, $existentLocations);
        $ids = $existentLocations;

        $this->deleteLocationsFromStore($locationsToDelete, $storeId);
    }

    /**
     * Delete vendors from flat table(s)
     *
     * @param int|array $vendorId
     * @param null|int $storeId
     * @return void
     */
    public function deleteLocationsFromStore($vendorId, $storeId = null)
    {
        if (!is_array($vendorId)) {
            $vendorId = [$vendorId];
        }
        if (null === $storeId) {
            foreach ($this->storeManager->getStores() as $store) {
                $this->connection->delete(
                    $this->vendorIndexerHelper->getFlatTableName($store->getId()),
                    ['entity_id IN(?)' => $vendorId]
                );
            }
        } else {
            $this->connection->delete(
                $this->vendorIndexerHelper->getFlatTableName((int)$storeId),
                ['entity_id IN(?)' => $vendorId]
            );
        }
    }
}
 