<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 12/12/19
 * Time: 5:41 pm
 */
namespace Omnyfy\Vendor\Block\Location;

use Magento\Catalog\Model\Product\Visibility;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    protected $collectionFactory;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        array $data = [])
    {
        $this->collectionFactory = $collectionFactory;

        parent::__construct($context, $postDataHelper, $layerResolver, $categoryRepository, $urlHelper, $data);
    }

    /**
     * Product collection filtered by the location_id
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection|\Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    protected function _getProductCollection()
    {
        if ($this->_productCollection === null) {
            $locationId = intval($this->getRequest()->getParam('id',null));

            if ($locationId) {
                $layer = $this->getLayer();
                $this->_productCollection = $layer->getProductCollection();

                $inventoryTable = $this->_productCollection->getConnection()->getTableName('omnyfy_vendor_inventory');
                $innerSql = new \Zend_Db_Expr('SELECT product_id FROM ' . $inventoryTable . ' WHERE location_id=' . $locationId);
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
 