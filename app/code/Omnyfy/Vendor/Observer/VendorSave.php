<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 13/7/17
 * Time: 2:25 PM
 */
namespace Omnyfy\Vendor\Observer;

use Magento\Framework\Event\ObserverInterface;
use Omnyfy\Vendor\Api\Data\VendorInterface;
use Omnyfy\Vendor\Helper\Data as OmnyfyHelper;
use Omnyfy\Vendor\Model\Resource\Vendor as VendorResource;

class VendorSave implements ObserverInterface
{
    protected $vendorResource;

    protected $userFactory;

    protected $omnyfyHelper;

    protected $vendorTypeRepository;

    public function __construct(
        VendorResource $venderResource,
        \Magento\User\Model\UserFactory $userFactory,
        OmnyfyHelper $omnyfyHelper,
        \Omnyfy\Vendor\Api\VendorTypeRepositoryInterface $vendorTypeRepository
    )
    {
        $this->vendorResource = $venderResource;
        $this->userFactory = $userFactory;
        $this->omnyfyHelper = $omnyfyHelper;
        $this->vendorTypeRepository = $vendorTypeRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $vendor = $observer->getData('vendor');

        $roleIds = $this->omnyfyHelper->getRoleIdsByName(VendorInterface::VENDOR_ADMIN_ROLE);
        if (empty($roleIds)) {
            //'Role '. VendorInterface::VENDOR_ADMIN_ROLE . ' not exist.';
            //TODO: throw errors
            return;
        }

        //load user relation by vendor id, if not exist, try to create
        $userIds = $this->vendorResource->getUserIdsByVendorId($vendor->getId());
        if (!empty($userIds)) {
            $userCollection = $this->userFactory->create()->getCollection();
            $userCollection->addFieldToFilter('user_id', ['in' => $userIds]);

            foreach($userCollection as $user) {
                if (VendorInterface::VENDOR_ADMIN_ROLE == $user->getRole()->getRoleName()) {
                    return;
                }
            }
        }
        //TODO: search user by email
        if ($vendor->getEmail()) {
            $userCollection = $this->userFactory->create()->getCollection();
            $userCollection->addFieldToFilter('email', $vendor->getEmail());
            if ($userCollection->getSize() > 0) {
                $user = $userCollection->getFirstItem();
            }
        }

        if (empty($user) || $user->getId() ==0) {
            $user = $this->userFactory->create();
            $user->setData([
                'username' => $vendor->getEmail(),
                'firstname' => $vendor->getName(),
                'lastname' => 'admin',
                'email' => $vendor->getEmail(),
                'password' => $vendor->getEmail(). '2017',
                'is_active' => 1
            ]);

            $user->setRoleId($roleIds[0]);
            $user->save();
        }

        if ($user->getId()) {
            $this->vendorResource->saveUserRelation([
                'user_id' => $user->getId(),
                'vendor_id' => $vendor->getId()
            ]);
        }

        $vendorType = $this->vendorTypeRepository->getById($vendor->getTypeId());
        $this->vendorResource->updateLocationAttributeSetId(
            $vendor->getId(),
            $vendorType->getLocationAttributeSetId(),
            $vendorType->getId()
        );
    }
}