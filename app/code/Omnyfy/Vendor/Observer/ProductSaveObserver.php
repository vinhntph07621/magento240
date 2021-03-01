<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 17/6/17
 * Time: 6:20 PM
 */

namespace Omnyfy\Vendor\Observer;

use Magento\Framework\Event\ObserverInterface;
use Omnyfy\Vendor\Model\Resource\Vendor as VendorResource;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Omnyfy\Vendor\Api\Data\VendorInterface;

class ProductSaveObserver implements ObserverInterface
{
    protected $vendorResource;

    protected $authSession;

    protected $_inventoryResource;

    protected $_config;

    protected $_state;

    public function __construct(
        VendorResource $vendorResource,
        AuthSession $authSession,
        \Omnyfy\Vendor\Model\Resource\Inventory $inventoryResource,
        \Omnyfy\Vendor\Model\Config $config,
        \Magento\Framework\App\State $state
    )
    {
        $this->vendorResource = $vendorResource;

        $this->authSession = $authSession;

        $this->_inventoryResource = $inventoryResource;

        $this->_config = $config;

        $this->_state = $state;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try{
            $code = $this->_state->getAreaCode();
            if ($code !== \Magento\Framework\App\Area::AREA_ADMINHTML && $code !== \Magento\Framework\App\Area::AREA_ADMINHTML) {
                return;
            }
        }
        catch (\Exception $e) {
            return;
        }

        $role = $this->authSession->getUser()->getRole()->getRoleName();

        $productId = $observer->getProduct()->getId();

        $originVendorIds = $this->vendorResource->getVendorIdArrayByProductId($productId);
        $object = $observer->getDataObject();

        $vendorIds = $object->getVendorIds();

        //get vendor id for this user
        $userId = $this->authSession->getUser()->getId();

        // Check if the current user is a vendor
        $vendorId = $this->vendorResource->getVendorIdByUserId($userId);

        // If the current user is not a vendor
        if (empty($vendorId)) {
            //Vendor user but missed their vendor config, should not change.

            // Check if the user is an administrator/MO role
            $adminRoles = [
                'Administrators',
                'marketplace_owner'
            ];

            if (!in_array($role, $adminRoles)) {
                return;
            }

            //For admin user, save from object data if set
            if (null ==$vendorIds || false === $vendorIds) {
                return;
            }

            //If config set as not allow
            if (!$this->_config->isAdminAcrossVendor()) {
                return;
            }

            $vendorIds = is_array($vendorIds) ? $vendorIds : [$vendorIds];
            $toRemove = array_diff($originVendorIds, $vendorIds);
            $toAdd = array_diff($vendorIds, $originVendorIds);

            //Only remove if product can only belongs to a single vendor
            if (!empty($toRemove) &&
                !$this->_config->isVendorShareProducts()
            ) {
                //2018-09-05 16:32 Jing Xiao
                //To remove inventory from all locations of old vendor
                $this->_inventoryResource->removeByVendorIds([$productId], $toRemove);

                $this->vendorResource->remove(
                    ['product_id' => $productId, 'vendor_id' => $toRemove],
                    'omnyfy_vendor_vendor_product'
                );
            }
            if (!empty($toAdd)) {
                $p2v = [];
                foreach($toAdd as $vId) {
                    $p2v[] = ['product_id' => $productId, 'vendor_id' => $vId];
                }
                $this->vendorResource->saveProductRelation($p2v);
            }
        }
        else{
            //For vendor user, only try to save current vendor-product relationship
            $this->vendorResource->saveProductRelation(['product_id' => $productId, 'vendor_id' => $vendorId]);
        }
    }
}
