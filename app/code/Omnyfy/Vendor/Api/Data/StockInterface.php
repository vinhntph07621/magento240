<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-17
 * Time: 15:56
 */
namespace Omnyfy\Vendor\Api\Data;

interface StockInterface
{
    const PRODUCT_ID = 'product_id';

    const QTY = 'qty';

    /**
     * @return int
     */
    public function getProductId();

    /**
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * @return string
     */
    public function getQty();

    /**
     * @param string $qty
     * @return $this
     */
    public function setQty($qty);
}
 