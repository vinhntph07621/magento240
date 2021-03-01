<?php
/**
 * Project: Multi Vendor M2.
 * User: abhay
 * Date: 15/3/18
 * Time: 3:54 PM
 */
namespace Omnyfy\Vendor\Block\Location\View;

use Magento\Framework\View\Element\Template;

class Services extends Template{

    protected $_resource;

    protected $storeRepository;
    protected $collectionFactory;
    protected $vendorInventory;

    protected $store;

    public function __construct(
		Template\Context $context,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
		\Omnyfy\Vendor\Model\InventoryFactory $vendorInventory,
        \Magento\Store\Api\StoreRepositoryInterface $storeRepository,
        \Magento\Framework\App\ResourceConnection $resource,
        array $data = [])
    {
        $this->_resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->vendorInventory = $vendorInventory;
        $this->storeRepository = $storeRepository;
        parent::__construct($context, $data);
    }

    public function getCollection()
    {
		$locationId = $this->getRequest()->getParam('id');
		
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $collection */
		$collection = $this->collectionFactory->create();                             
		$joinConditions = 'e.entity_id = omnyfy_vendor_inventory.product_id';
		$collection->addAttributeToSelect('*');
		$collection->getSelect()->join(
					 ['omnyfy_vendor_inventory'],
					 $joinConditions,
					 []
					)->columns("omnyfy_vendor_inventory.location_id")
					  ->where("omnyfy_vendor_inventory.location_id=".$locationId);	
		
		
        /* $collection = $this->collectionFactory->create();
        $collection->addAttributeToSelect('*');

        if ($this->getStore()) {
            $collection->setStore($this->getStore());
        }

        $locationId = $this->getRequest()->getParam('id');

        if (!empty($locationId)) {
            $inventoryTable = $this->_resource->getTableName('omnyfy_vendor_inventory');
            $subSql = 'SELECT product_id FROM '.$inventoryTable. ' WHERE location_id =?';

            $collection->addFieldToFilter('entity_id',
                [
                    'nin' => new \Zend_Db_Expr($this->_resource->getConnection()->quoteInto($subSql, $locationId))
                ]
            );
            #$collection->setFlag('has_location_filter');
        } */
		
		#echo $collection->getSelect();
		
		#$vandorId = $vendorId;

        /* if (!empty($vandorId) && !$collection->getFlag('has_vendor_filter')) {
            $collection->addFieldToFilter('vendor_id', $vandorId);
            $collection->setFlag('has_vendor_filter');
        } */

        return $collection;
    }

    protected function getStore()
    {
        if (null !== $this->store) {
            return $this->store;
        }

        if (!($storeId = $this->_request->getParam('current_store_id'))) {
            return null;
        }

        return $this->store = $this->storeRepository->getById($storeId);
    }
}