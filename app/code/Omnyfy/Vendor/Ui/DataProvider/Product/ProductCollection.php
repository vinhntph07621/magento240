<?php
/**
 * Project: LPO.
 * User: jing
 * Date: 26/6/18
 * Time: 12:48 PM
 */
namespace Omnyfy\Vendor\Ui\DataProvider\Product;

class ProductCollection extends \Omnyfy\Vendor\Model\Resource\Product\Collection
{
    protected function _productLimitationJoinPrice()
    {
        $this->_productLimitationFilters->setUsePriceIndex(false);
        return $this->_productLimitationPrice(true);
    }
}