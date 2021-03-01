<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 19/6/17
 * Time: 12:09 PM
 */
namespace Omnyfy\Vendor\Observer;

use Magento\Framework\Event\ObserverInterface;
use Omnyfy\Vendor\Model\Resource\Vendor as VendorResource;
use Omnyfy\Vendor\Model\Resource\Profile as ProfileResource;
use Omnyfy\Vendor\Model\Resource\Location as LocationResource;
use Magento\Backend\Model\Auth\Session as AuthSession;
use Magento\Backend\Model\Session as BackendSession;
use Omnyfy\Vendor\Api\Data\VendorInterface;
use Omnyfy\Vendor\Helper\Data as VendorHelper;
use Omnyfy\Vendor\Helper\User as UserHelper;

class AdminLoginSucceeded implements ObserverInterface
{
    protected $vendorResource;

    protected $profileResource;

    protected $locationResource;

    protected $vendorHelper;

    protected $authSession;

    protected $backendSession;

    protected $userHelper;

    public function __construct(
            VendorResource $vendorResource,
            ProfileResource $profileResource,
            LocationResource $locationResource,
            VendorHelper $vendorHelper,
            AuthSession $authSession,
            BackendSession $backendSession,
            UserHelper $userHelper
        )
    {
        $this->vendorResource = $vendorResource;

        $this->profileResource = $profileResource;

        $this->locationResource = $locationResource;

        $this->vendorHelper = $vendorHelper;

        $this->authSession = $authSession;

        $this->backendSession = $backendSession;

        $this->userHelper = $userHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $user = $observer->getData('user');

        $role = $this->authSession->getUser()->getRole()->getRoleName();

        $adminRoles = [
            'Administrators',
            'marketplace_owner'
        ];

        $userRoles = $this->userHelper->getNonRestrictUser();

        $adminRoles = array_merge ($adminRoles, $userRoles);

        if (in_array($role, $adminRoles)) {
            return;
        }

        #$isVendorAdmin = VendorInterface::VENDOR_ADMIN_ROLE == $role ? true: false;
        $isVendorAdmin = true;
        $userId = $user->getId();
        //load vendor id by user id
        $vendorId = $this->vendorResource->getVendorIdByUserId($userId);

        //load profiles by vendor id, add user_profile filter for vendor users
        $profileIds = $this->profileResource->getProfileIdsByVendorId($vendorId, ($isVendorAdmin ? null : $userId));

        //load website ids by profile ids, add user_profile filter for vendor users
        $websiteIds = array_keys($profileIds);

        $storeIds = $this->vendorHelper->getStoreIdsByWebsiteIds($websiteIds);

        //load location ids by profile ids
        $locationIds = $this->locationResource->getLocationIdsByProfileIds(array_unique(array_values($profileIds)));

        $vendorTypeId = $this->vendorResource->getVendorTypeIdByVendorId($vendorId);

        $vendorName = $this->vendorResource->getVendorNameById($vendorId);
        $vendorName = empty($vendorName) ? 'Not Set' : $vendorName;

        //set all those ids into session
        $this->backendSession->setVendorInfo(
            [
                'vendor_id' => $vendorId,
                'is_vendor_admin' => $isVendorAdmin,
                'profile_ids' => $profileIds,
                'website_ids' => $websiteIds,
                'store_ids' => $storeIds,
                'location_ids' => $locationIds,
                'type_id' => $vendorTypeId,
                'vendor_name' => $vendorName
            ]
        );
    }
}
