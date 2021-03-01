<?php

namespace Omnyfy\Vendor\Plugin\Product;

use Magento\Backend\Model\Session as BackendSession;
use Magento\Framework\App\ResourceConnection;

class ProductMassAttribute
{
    /**
     * @var BackendSession
     */
    protected $backendSession;

    /**
     * @var
     */
    protected $_resource;

    public function __construct(
        BackendSession $backendSession,
        ResourceConnection $resource
    )
    {
        $this->backendSession = $backendSession;
        $this->_resource = $resource;
    }

    public function aroundGetProductIds(\Magento\Catalog\Helper\Product\Edit\Action\Attribute $subject, \Closure $proceed)
    {
        $productIdsSubmitted = $this->backendSession->getProductIds();

        $vendorInfo = $this->backendSession->getVendorInfo();

        if (!empty($vendorInfo)) {
            $connection = $this->_resource->getConnection();
            $tableName = $this->_resource->getTableName('omnyfy_vendor_vendor_product');
            $vendorTableProduct = "Select * FROM " . $tableName . ' WHERE vendor_id = ' . $vendorInfo['vendor_id'];
            // fetchOne it return the one value
            $vendorProducts = $connection->fetchAssoc($vendorTableProduct);

            $allowableIds = [];
            foreach ($vendorProducts as $vendorProduct) {
                foreach ($productIdsSubmitted as $productIdKey => $productId) {
                    if ($vendorProduct['product_id'] == $productId) {
                        array_push($allowableIds, $productId);
                    }
                }
            }

            return $allowableIds;
        }
        else {
            // call the core observed function
            $returnValue = $proceed();

            return $returnValue;
        }
    }
}