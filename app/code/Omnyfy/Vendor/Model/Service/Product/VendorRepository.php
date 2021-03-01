<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 21/11/19
 * Time: 2:17 pm
 */

namespace Omnyfy\Vendor\Model\Service\Product;

class VendorRepository extends AbstractRepository implements \Omnyfy\Vendor\Api\VendorProductRepositoryInterface
{
    protected $_vendorResource;

    protected $_inventoryResource;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Vendor\Model\Config $config,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Omnyfy\Vendor\Model\Resource\Inventory $inventoryResource
    ) {
        $this->_vendorResource = $vendorResource;
        $this->_inventoryResource = $inventoryResource;
        parent::__construct($productResource, $logger, $config);
    }

    public function getByProduct($productId)
    {
        $skuArray = $this->_productResource->getProductsSku([$productId]);
        if (empty($skuArray)) {
            return $this->error('Product with id [' . $productId . '] not exist');
        }

        $vendorIds = $this->_vendorResource->getVendorIdArrayByProductId($productId);

        return ['vendor_ids' => $vendorIds];
    }

    public function assignToVendor($productId, $vendorId)
    {
        $skuArray = $this->_productResource->getProductsSku([$productId]);
        if (empty($skuArray)) {
            return $this->error('Product with id [' . $productId . '] not exist');
        }

        $allVendorIds = $this->_vendorResource->getAllVendorIds($this->_config->isQtyActiveVendorOnly());

        if (!in_array($vendorId, $allVendorIds)) {
            return $this->error('Vendor #'. $vendorId. ' does not exist');
        }

        $vendorIds = $this->_vendorResource->getVendorIdArrayByProductId($productId);
        $vendorIds[] = $vendorId;
        $vendorIds = array_unique($vendorIds);

        if (!$this->_config->isVendorShareProducts() && count($vendorIds) > 1) {
            return $this->error('Multiple vendors to associate but not allowed in configuration');
        }

        $data = [];
        foreach($vendorIds as $_vendorId) {
            $data[] = [
                'product_id' => $productId,
                'vendor_id' => $_vendorId
            ];
        }
        $this->_vendorResource->saveProductRelation($data);

        return $this->success('Assigned product [' . $productId . '] to vendor #' . $vendorId);
    }

    public function updateByProduct($productId, $vendorIds)
    {
        $skuArray = $this->_productResource->getProductsSku([$productId]);
        if (empty($skuArray)) {
            return $this->error('Product with id [' . $productId . '] not exist');
        }

        $allVendorIds = $this->_vendorResource->getAllVendorIds();

        $errors = [];
        $notExist = array_diff($vendorIds, $allVendorIds);
        if (!empty($notExist)) {
            foreach($notExist as $_vendorId) {
                $errors[] = 'Vendor #'. $_vendorId . ' does not exist';
            }
        }

        if (!empty($errors)) {
            return $this->error($errors);
        }

        $existVendorIds = $this->_vendorResource->getVendorIdArrayByProductId($productId);

        $resultVendorIds = array_unique($vendorIds);

        if (!$this->_config->isVendorShareProducts() && count($resultVendorIds) > 1) {
            return $this->error('Multiple vendors provided but not allowed in configuration');
        }

        $toRemoveVendorRelation = array_diff($existVendorIds, $vendorIds);
        $toAddVendorRelation = [];
        foreach($resultVendorIds as $vendorId) {
            $toAddVendorRelation[] = [
                'product_id' => $productId,
                'vendor_id' => $vendorId
            ];
        }

        //Submit all changes as a transaction
        $conn = $this->_vendorResource->getConnection();
        $conn->beginTransaction();
        try {
            if (!empty($toAddVendorRelation)) {
                $this->_vendorResource->saveProductRelation($toAddVendorRelation);
            }

            if (!empty($toRemoveVendorRelation)) {
                $this->_vendorResource->remove(
                    [
                        'product_id' => $productId,
                        'vendor_id' => $toRemoveVendorRelation
                    ],
                    $conn->getTableName('omnyfy_vendor_vendor_product')
                );

                $this->_inventoryResource->removeByVendorIds([$productId], $toRemoveVendorRelation);
            }

            $conn->commit();
        }
        catch(\Exception $e) {
            $conn->rollBack();
            return $this->error($e->getMessage());
        }

        return $this->success('Updated vendor relation for product [' . $productId . ']');
    }

    public function removeRelation($productId, $vendorId)
    {
        $skuArray = $this->_productResource->getProductsSku([$productId]);
        if (empty($skuArray)) {
            return $this->error('Product with id [' . $productId . '] not exist');
        }

        $vendorName = $this->_vendorResource->getVendorNameById($vendorId);
        if (empty($vendorName)) {
            return $this->error('Vendor #'. $vendorId. ' does not exist');
        }

        $conn = $this->_vendorResource->getConnection();
        $conn->beginTransaction();
        try {
            $this->_inventoryResource->removeByVendorIds([$productId], [$vendorId]);

            $this->_vendorResource->remove(
                [
                    'product_id' => $productId,
                    'vendor_id' => $vendorId
                ],
                $conn->getTableName('omnyfy_vendor_vendor_product')
            );

            $conn->commit();
        }
        catch(\Exception $e) {
            $conn->rollBack();
            return $this->error($e->getMessage());
        }

        return $this->success('Removed relation between product [' . $productId . '] and vendor #' . $vendorId);
    }
}