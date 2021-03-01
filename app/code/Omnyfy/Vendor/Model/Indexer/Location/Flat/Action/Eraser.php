<?php

namespace Omnyfy\Vendor\Model\Indexer\Location\Flat\Action;

class Eraser
{

    /**
     * @var \Omnyfy\Vendor\Helper\Location\Flat\Indexer
     */
    protected $locationIndexerHelper;

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
     * @param \Omnyfy\Vendor\Helper\Location\Flat\Indexer $locationHelper
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Omnyfy\Vendor\Helper\Location\Flat\Indexer $locationHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->locationIndexerHelper = $locationHelper;
        $this->connection = $resource->getConnection();
        $this->storeManager = $storeManager;
    }

    /**
     * Remove locations from flat that are not exist
     *
     * @param array $ids
     * @param int $storeId
     * @return void
     */
    public function removeDeletedLocations(array &$ids, $storeId)
    {
        $select = $this->connection->select()->from(
            $this->locationIndexerHelper->getTable('omnyfy_vendor_location_entity')
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
     * Delete locations from flat table(s)
     *
     * @param int|array $locationId
     * @param null|int $storeId
     * @return void
     */
    public function deleteLocationsFromStore($locationId, $storeId = null)
    {
        if (!is_array($locationId)) {
            $locationId = [$locationId];
        }
        if (null === $storeId) {
            foreach ($this->storeManager->getStores() as $store) {
                $this->connection->delete(
                    $this->locationIndexerHelper->getFlatTableName($store->getId()),
                    ['entity_id IN(?)' => $locationId]
                );
            }
        } else {
            $this->connection->delete(
                $this->locationIndexerHelper->getFlatTableName((int)$storeId),
                ['entity_id IN(?)' => $locationId]
            );
        }
    }

}
