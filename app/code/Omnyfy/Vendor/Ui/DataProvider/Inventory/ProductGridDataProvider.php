<?php
/**
 * Project: Multi Vendors.
 * User: jing
 * Date: 1/2/18
 * Time: 10:14 AM
 */
namespace Omnyfy\Vendor\Ui\DataProvider\Inventory;


class ProductGridDataProvider extends \Magento\Catalog\Ui\DataProvider\Product\ProductDataProvider
{
    protected $_backendSession;

    protected $_resource;

    protected $request;

    protected $storeRepository;

    protected $store;

    protected $vendorConfig;

    protected $isVendorShareProduct;

    protected $isAdminSeeAllSku;

    public function __construct(
        string $name,
        string $primaryFieldName,
        string $requestFieldName,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Framework\App\ResourceConnection $resource,
        \Omnyfy\Vendor\Model\Config $vendorConfig,
        $addFieldStrategies = [],
        $addFilterStrategies = [],
        array $meta = [],
        array $data = [])
    {
        $this->_backendSession = $backendSession;
        $this->_resource = $resource;
        $this->request = $request;
        $this->storeRepository = $storeRepository;
        $this->vendorConfig = $vendorConfig;
        $this->isVendorShareProduct = $vendorConfig->isVendorShareProducts();
        $this->isAdminSeeAllSku = $vendorConfig->isAdminSeeAllSku();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory, $addFieldStrategies, $addFilterStrategies, $meta, $data);
    }

    public function getCollection()
    {
        /** @var Collection $collection */
        $collection = parent::getCollection();
        $collection->addAttributeToSelect('status');

        if ($this->getStore()) {
            $collection->setStore($this->getStore());
        }

        $locationId = $this->_backendSession->getCurrentLocationId();

        if (!empty($locationId) && !$collection->getFlag('has_location_filter')) {
            $inventoryTable = $this->_resource->getTableName('omnyfy_vendor_inventory');
            $subSql = 'SELECT product_id FROM '.$inventoryTable. ' WHERE location_id =?';

            $collection->addFieldToFilter('entity_id',
                [
                    'nin' => new \Zend_Db_Expr($this->_resource->getConnection()->quoteInto($subSql, $locationId))
                ]
            );

            if (!$this->isVendorShareProduct && !$this->isAdminSeeAllSku) {
                $vendorProductTable = $this->_resource->getTableName('omnyfy_vendor_vendor_product');
                $locationTable = $this->_resource->getTableName('omnyfy_vendor_location_entity');
                $subSql = 'SELECT product_id FROM '. $vendorProductTable
                    . ' WHERE vendor_id in (SELECT vendor_id FROM '. $locationTable . ' WHERE entity_id =?)';

                $collection->addFieldToFilter('entity_id', [
                    'in' => new \Zend_Db_Expr($this->_resource->getConnection()->quoteInto($subSql, $locationId))
                ]);
            }

            $collection->setFlag('has_location_filter', 1);
        }

        //2018-06-26 vendor filter already injected in \Omnyfy\Vendor\Model\Resource\Product\Collection
        //And vendor_id is not an attribute for product.

        return $collection;
    }

    protected function getStore()
    {
        if (null !== $this->store) {
            return $this->store;
        }

        if (!($storeId = $this->request->getParam('current_store_id'))) {
            return null;
        }

        return $this->store = $this->storeRepository->getById($storeId);
    }
}