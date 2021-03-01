<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-17
 * Time: 14:38
 */
namespace Omnyfy\Vendor\Model;

use Magento\Store\Model\StoreManagerInterface;

class StockRepository implements \Omnyfy\Vendor\Api\StockRepositoryInterface
{
    protected $stockManager;

    protected $productResource;

    protected $inventoryResource;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $stockManager,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Omnyfy\Vendor\Model\Resource\Inventory $inventoryResource
    )
    {
        $this->stockManager = $stockManager;
        $this->productResource = $productResource;
        $this->inventoryResource = $inventoryResource;
    }

    public function getStockInfo($productId, $qty)
    {
        if (empty($productId)) {
            return ['status' => false, 'available' => 0];
        }
        $websiteId = $this->stockManager->getWebsite()->getId();
        $vendorId = 0;
        $qtys = $this->inventoryResource->loadInventoryGroupedByLocation($productId, $websiteId, $vendorId);
        if (empty($qtys)) {
            return ['status' => false, 'available' => 0];
        }

        $status = false;
        $available = 0;
        //$total = 0;

        foreach($qtys as $locationId => $stockQty) {
            if ($stockQty >= $qty) {
                $status = true;
            }
            if ($stockQty > $available) {
                $available = $stockQty;
            }
            //$total += $stockQty;
        }

        return ['status' => $status, 'available' => $available];
    }

    public function getStockInfoBySku($sku, $qty)
    {
        $productId = $this->productResource->getIdBySku($sku);
        return $this->getStockInfo(intval($productId), $qty);
    }

    public function getList($data)
    {
        if (empty($data)) {
            return ['error' => true, 'message' => 'Invalid Data provided'];
        }

        $productIds = [];
        foreach($data as $_stock) {
            $productIds[$_stock->getProductId()] = $_stock->getQty();
        }

        if (empty($productIds)) {
            return ['error' => true, 'message' => 'Invalid products provided'];
        }

        $websiteId = $this->stockManager->getWebsite()->getId();
        $stockData = $this->inventoryResource->loadQtysByProductIds(array_keys($productIds), $websiteId);

        $result = [];
        foreach($productIds as $productId => $qty) {
            $status = false;
            $available = 0;
            if (array_key_exists($productId, $stockData)) {
                foreach($stockData[$productId] as $locationId => $stockQty) {
                    if ($stockQty >= $qty) {
                        $status = true;
                    }
                    if ($stockQty > $available) {
                        $available = $stockQty;
                    }
                }
            }

            $result[] = ['product_id' => $productId, 'status' => $status, 'available' => $available];
        }
        $stockData = null;
        $productIds = null;
        return $result;
    }
}
 