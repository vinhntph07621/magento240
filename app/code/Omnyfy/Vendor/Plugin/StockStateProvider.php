<?php
/**
 * Project: Omnyfy Multi Vendor.
 * User: jing
 * Date: 26/4/17
 * Time: 5:38 PM
 */

namespace Omnyfy\Vendor\Plugin;

class StockStateProvider
{
    protected $helper;

    protected $_locationMode;

    protected $_locationResource;

    protected $_warehouseIds;

    protected $_isProductAcrossVendors;

    protected $_locationIdToVendorIds;

    protected $_moVendorIds;

    protected $notCheckTypes = [
        \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE,
        'booking'
    ];

    public function __construct(
        \Omnyfy\Vendor\Helper\Session $helper,
        \Omnyfy\Vendor\Model\Config $config,
        \Omnyfy\Vendor\Model\Resource\Location $locationResource
    )
    {
        $this->helper = $helper;
        $this->_locationMode = $config->getQtyCheckMode();
        $this->_isProductAcrossVendors = $config->isVendorShareProducts();
        $this->_locationResource = $locationResource;
        $this->_locationIdToVendorIds = $locationResource->getAllLocationIdsToVendorIds();
        $this->_moVendorIds = $config->getMOVendorIds();
    }

    protected function getWarehouseIds()
    {
        if (null == $this->_warehouseIds) {
            $this->_warehouseIds = $this->_locationResource->getWarehouseIds();
            $this->_warehouseIds = empty($this->_warehouseIds) ? [] : $this->_warehouseIds;
        }

        return $this->_warehouseIds;
    }

    public function aroundCheckQty(\Magento\CatalogInventory\Model\StockStateProvider $subject,
        callable $proceed,
        $stockItem,
        $qty
        )
    {
        //Only check for simple product and qty in stock item (normally it's total qty of all warehouses) is OK
        //2017-09-16 22:36 Jing Xiao.
        //Qty for magento default may not set, so always check with omnyfy multiple warehouse for simple products
        //2017-11-09 15:28 Jing Xiao
        //Need to check inventory for virtual products, VIRTUAL ????
        //2108-06-01 20:51 Jing Xiao
        //For any new product type, we should check, so just ignore bundle and configurable products
        //2019-09-30 11:02 Jing Xiao
        //For all product types we may need to override vendor_id by session
        $sessionVendorId = $stockItem->getSessionVendorId();
        if (!empty($sessionVendorId)) {
            if (empty($stockItem->getVendorId())
                || (!empty($this->_moVendorIds) && in_array($stockItem->getVendorId(), $this->_moVendorIds))) {
                $stockItem->setData('vendor_id', $sessionVendorId);
            }
        }

        if ( !in_array($stockItem->getTypeId(), $this->notCheckTypes)) {
            if ($stockItem->hasData('qtys')) {
                $qtys = $stockItem->getQtys();
            }
            //2017-09-16 22:42 Jing Xiao.
            //website id of stockitem seems always 0.
            //So we can NOT found any inventory below
            /*
            if (empty($qtys)) {
                $qtys = $this->helper->groupInventoryByLocationId(
                    $stockItem->getProductId(),
                    $stockItem->getWebsiteId(),
                    $vendorId
                );
            }
            if (!empty($vendorId)){
                $stockItem->setData('vendor_id', $vendorId);
            }
            */

            if (empty($qtys)) {
                return false;
            }

            $locationId = $stockItem->getSessionLocationId();
            $shipFromWarehouse = boolval($stockItem->getShipFromWarehouseFlag());
            if (!empty($locationId) && !$shipFromWarehouse) {
                $stockItem->setData('location_id', $locationId);

                if (isset($qtys[$locationId])) {
                    $stockQty = $qtys[$locationId];
                    if ($stockQty - $stockItem->getMinQty() - $qty >= 0) {
                        return $proceed($stockItem, $qty);
                    }
                }

                return false;
            }

            // load all warehouse locations' ID
            $warehouseIds = $this->getWarehouseIds();

            foreach($qtys as $locationId => $stockQty) {

                if ((\Omnyfy\Vendor\Model\Config::QTY_CHECK_MODE_WAREHOUSE_ONLY == $this->_locationMode
                        || $shipFromWarehouse
                    )
                    && !in_array($locationId, $warehouseIds)
                )
                {
                    continue;
                }

                //2018-05-25 11:04 Jing Xiao
                //if product set to not manage stock, take the first location
                if (!$stockItem->getManageStock()) {
                    $stockItem->setData('location_id', $locationId);
                    $this->parseVendorId($locationId, $sessionVendorId, $stockItem);
                    return $proceed($stockItem, $qty);
                }

                if ($stockQty - $stockItem->getMinQty() - $qty >= 0) {
                    $stockItem->setData('location_id', $locationId);
                    $this->parseVendorId($locationId, $sessionVendorId, $stockItem);
                    return $proceed($stockItem, $qty);
                }
            }

            //FOR not manage stock items, if not in stock in warehouse
            if (!$stockItem->getManageStock()) {
                //TODO: 0 for location_id maybe not a good idea, let's see
                $stockItem->setData('location_id', 0);
                return $proceed($stockItem, $qty);
            }

            switch ($stockItem->getBackorders()) {
                case \Magento\CatalogInventory\Model\Stock::BACKORDERS_YES_NONOTIFY:
                case \Magento\CatalogInventory\Model\Stock::BACKORDERS_YES_NOTIFY:
                    break;
                default:
                    return false;
            }
        }

        return $proceed($stockItem, $qty);
    }

    protected function parseVendorId($locationId, $sessionVendorId, &$stockItem)
    {
        if (!empty($sessionVendorId) && empty($this->_moVendorIds)) {
            return;
        }

        if (array_key_exists($locationId, $this->_locationIdToVendorIds)) {
            $vendorId = $this->_locationIdToVendorIds[$locationId];
            if (!empty($vendorId)) {
                //if it's been configured in MO vendor_ids, overwrite with session vendorId
                if (!empty($sessionVendorId) && in_array($vendorId, $this->_moVendorIds)) {
                    $stockItem->setData('vendor_id', $sessionVendorId);
                }
                else {
                    //for normal product
                    $stockItem->setData('vendor_id', $vendorId);
                }
            }
        }
    }
}
