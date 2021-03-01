<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 14/8/17
 * Time: 10:37 AM
 */
namespace Omnyfy\Vendor\Block\Vendor;

use Magento\Catalog\Model\Product\Visibility;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    protected $collectionFactory;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        array $data = [])
    {
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $vendorId = intval($this->getRequest()->getParam('id',null));

            if ($vendorId) {
                $layer = $this->getLayer();
                $this->_productCollection = $layer->getProductCollection();

                $inventoryTable = $this->_productCollection->getConnection()->getTableName('omnyfy_vendor_vendor_product');
                $innerSql = new \Zend_Db_Expr('SELECT product_id FROM ' . $inventoryTable . ' WHERE vendor_id=' . $vendorId);
                $locationProductIds = $this->_productCollection->getConnection()->fetchCol($innerSql);

                if (count($locationProductIds) > 0) {
                    $locationProductIds = implode(",", array_values($locationProductIds));
                    $this->_productCollection->getSelect()->where("e.entity_id in (" . $locationProductIds . ")");
                } else {
                    $locationProductIds = 0;
                    $this->_productCollection->getSelect()->where("e.entity_id in (" . $locationProductIds . ")");
                }
            }
        }
        return $this->_productCollection;
    }
}
