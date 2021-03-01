<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-17
 * Time: 14:33
 */
namespace Omnyfy\Vendor\Api;

interface StockRepositoryInterface
{
    /**
     * load stock information by specified product ID
     * @param string $productId
     * @param string $qty
     * @return \Omnyfy\Core\Api\Json
     */
    public function getStockInfo($productId, $qty);

    /**
     * Load Stock information by SKU
     *
     * @param string $sku
     * @param string $qty
     * @return \Omnyfy\Core\Api\Json
     */
    public function getStockInfoBySku($sku, $qty);

    /**
     *
     * @param \Omnyfy\Vendor\Api\Data\StockInterface[] $data
     * @return \Omnyfy\Core\Api\Json
     */
    public function getList($data);
}
 