<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-17
 * Time: 16:33
 */
namespace Omnyfy\Vendor\Model;

class Stock extends \Magento\Framework\DataObject implements \Omnyfy\Vendor\Api\Data\StockInterface
{
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    public function setQty($qty)
    {
        return $this->setData(self::QTY, $qty);
    }
}
 