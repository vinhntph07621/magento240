<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 21/11/19
 * Time: 2:19 pm
 */
namespace Omnyfy\Vendor\Model\Service\Product;

use Omnyfy\Vendor\Model\Resource\Inventory;

class LocationRepository extends AbstractRepository implements \Omnyfy\Vendor\Api\LocationProductRepositoryInterface
{
    protected $_inventoryCollectionFactory;

    protected $_locationResource;

    protected $_inventoryResource;

    protected $_vendorResource;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Vendor\Model\Config $config,
        \Omnyfy\Vendor\Model\Resource\Inventory\CollectionFactory $_inventoryCollectionFactory,
        \Omnyfy\Vendor\Model\Resource\Location $locationResource,
        \Omnyfy\Vendor\Model\Resource\Inventory $inventoryResource,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource
    )
    {
        $this->_inventoryCollectionFactory = $_inventoryCollectionFactory;
        $this->_locationResource = $locationResource;
        $this->_inventoryResource = $inventoryResource;
        $this->_vendorResource = $vendorResource;

        parent::__construct($productResource, $logger, $config);
    }

    public function getByProduct($productId)
    {
        $skuArray = $this->_productResource->getProductsSku([$productId]);
        if (empty($skuArray)) {
            return $this->error('Product with id [' . $productId . '] not exist');
        }

        $collection = $this->_inventoryCollectionFactory->create();
        $collection->addFieldToFilter('product_id', $productId);
        $arr = [];
        $count = 0;
        $total = 0;
        foreach($collection as $inventory) {
            $arr[] = [
                'location_id' => $inventory->getLocationId(),
                'qty' => $inventory->getQty().""
            ];
            $count++;
            $total += $inventory->getQty();
        }
        return [
            'inventory' => $arr,
            'count' => $count,
            'total' => $total
        ];
    }

    public function createInventory($productId, $inventories)
    {
        $skuArr = $this->_productResource->getProductsSku([$productId]);
        if (empty($skuArr)) {
            return $this->error('Product with id #' . $productId . ' not exist');
        }

        try {
            $allVendorIds = $this->_vendorResource->getAllVendorIds($this->_config->isQtyActiveVendorOnly());
            $locationId2VendorId = $this->_locationResource->getAllLocationIdsToVendorIds($this->_config->isQtyActiveLocationOnly());
            $existVendorRelation = $this->_vendorResource->getVendorIdArrayByProductId($productId);

            $data = [];
            $errors = [];

            $vendorIds = [];
            $zendDbExprNull = new \Zend_Db_Expr('NULL');
            foreach ($inventories as $_inventory) {
                if (!array_key_exists($_inventory->getLocationId(), $locationId2VendorId)) {
                    $errors[] = 'Location #'.$_inventory->getLocationId().' not exist.';
                    continue;
                }

                $vendorId = $locationId2VendorId[$_inventory->getLocationId()];
                if (!in_array($vendorId, $allVendorIds)) {
                    $errors[] = 'Vendor #' . $vendorId . ' not exist or not active';
                    continue;
                }

                if (!in_array($vendorId, $existVendorRelation)) {
                    $errors[] = 'Vendor #'. $vendorId . ' for location #'. $_inventory->getLocationId(). ' not assigned to [' . $productId . ']';
                    continue;
                }

                $vendorIds[$vendorId] = 1;

                $data[] = [
                    'inventory_id' => $zendDbExprNull,
                    'product_id' => $productId,
                    'location_id' => $_inventory->getLocationId(),
                    'qty' => $_inventory->getQty()
                ];
            }

            if (!$this->_config->isVendorShareProducts() && count($vendorIds) > 1) {
                $errors[] = 'Multiple vendors provided but not allowed in configuration';
            }

            if (!empty($errors)) {
                return $this->error($errors);
            }

            $this->_inventoryResource->bulkSave($data);
        }
        catch (\Exception $e) {
            return $this->error($e->getMessage());
        }

        return $this->success(count($data) . ' row(s) inventory processed');
    }

    public function updateInventory($productId, $inventories)
    {
        $skuArr = $this->_productResource->getProductsSku([$productId]);
        if (empty($skuArr)) {
            return $this->error('Product with id [' . $productId . '] not exist');
        }

        $allVendorIds = $this->_vendorResource->getAllVendorIds($this->_config->isQtyActiveVendorOnly());

        $locationId2VendorId = $this->_locationResource->getAllLocationIdsToVendorIds($this->_config->isQtyActiveLocationOnly());

        $existVendorRelation = $this->_vendorResource->getVendorIdArrayByProductId($productId);

        $existInventory = [];
        $collection = $this->_inventoryCollectionFactory->create();
        $collection->addFieldToFilter('product_id', $productId);
        foreach($collection as $_stockItem) {
            $existInventory[$_stockItem->getLocationId()] = $_stockItem->getInventoryId();
        }

        $data = [];
        $errors = [];
        $toRemoveInventory = [];
        $toRemoveVendorRelation = [];
        $toAddVendorRelation = [];

        $locationIds = [];
        $vendorIds = [];
        $zendDbExprNull = new \Zend_Db_Expr('NULL');
        foreach ($inventories as $_inventory) {
            if (!array_key_exists($_inventory->getLocationId(), $locationId2VendorId)) {
                $errors[] = 'Location #'.$_inventory->getLocationId().' not exist';
                continue;
            }

            $vendorId = $locationId2VendorId[$_inventory->getLocationId()];
            if (!in_array($vendorId, $allVendorIds)) {
                $errors[] = 'Vendor #' . $vendorId . ' not exist';
                continue;
            }

            if (!in_array($vendorId, $existVendorRelation)) {
                $toAddVendorRelation[] = [
                    'product_id' => $productId,
                    'vendor_id' => $vendorId
                ];
            }

            $data[] = [
                'inventory_id' => $zendDbExprNull,
                'product_id' => $productId,
                'location_id' => $_inventory->getLocationId(),
                'qty' => $_inventory->getQty()
            ];
            $locationIds[] = $_inventory->getLocationId();
            $vendorIds[] = $vendorId;
        }

        if (!$this->_config->isVendorShareProducts() && count($vendorIds) > 1) {
            $errors[] = 'Multiple vendors provided but not allowed in configuration';
        }

        if (!empty($errors)) {
            return $this->error($errors);
        }

        foreach($existInventory as $locationId => $inventoryId) {
            if (!in_array($locationId, $locationIds)) {
                $toRemoveInventory[] = $inventoryId;
            }
        }
        foreach($existVendorRelation as $vendorId) {
            if (!in_array($vendorId, $vendorIds)) {
                $toRemoveVendorRelation[] = $vendorId;
            }
        }

        //Submit all changes as a transaction
        $conn = $this->_vendorResource->getConnection();
        $conn->beginTransaction();
        try {
            $this->_inventoryResource->bulkSave($data);

            if (!empty($toRemoveInventory)) {
                $this->_inventoryResource->remove(['inventory_id' => $toRemoveInventory]);
            }

            if (!empty($toRemoveVendorRelation)) {
                $this->_vendorResource->remove(
                    [
                        'product_id' => $productId,
                        'vendor_id' => $toRemoveVendorRelation
                    ],
                    $conn->getTableName('omnyfy_vendor_vendor_product')
                );
            }

            if (!empty($toAddVendorRelation)) {
                $this->_vendorResource->saveProductRelation($toAddVendorRelation);
            }

            $conn->commit();
        }
        catch(\Exception $e) {
            $conn->rollBack();
            return $this->error($e->getMessage());
        }

        return $this->success('Updated inventory for product ['. $productId . ']');
    }

    public function removeRelation($productId, $locationId)
    {
        $skuArr = $this->_productResource->getProductsSku([$productId]);
        if (empty($skuArr)) {
            return $this->error('Product with id [' . $productId . '] not exist');
        }

        $vendorIds = $this->_locationResource->getVendorIdsByLocationIds([$locationId], $this->_config->isQtyActiveLocationOnly());
        if (!array_key_exists($locationId, $vendorIds)) {
            return $this->error('Location with id #' . $locationId . ' not exist');
        }

        $this->_inventoryResource->remove([
            'product_id' => $productId,
            'location_id' => $locationId
        ]);

        return $this->success('Removed product [' . $productId . '] from location #' . $locationId);
    }
}
 