<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-10
 * Time: 14:48
 */
namespace Omnyfy\VendorSubscription\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Omnyfy\VendorSubscription\Model\Resource\Plan\Collection;
use Omnyfy\VendorSubscription\Model\Source\UpdateStatus;

class Data extends AbstractHelper
{
    protected $resource;

    protected $planFactory;

    protected $planResource;

    protected $planCollectionFactory;

    protected $subscriptionFactory;

    protected $subscriptionCollectionFactory;

    protected $currency;

    protected $roleCollectionFactory;

    protected $_qHelper;

    protected $_vendorResource;

    protected $usageFactory;

    protected $updateCollectionFactory;

    protected $updateResource;

    public function __construct(
        Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Omnyfy\VendorSubscription\Model\PlanFactory $planFactory,
        \Omnyfy\VendorSubscription\Model\Resource\Plan $planResource,
        \Omnyfy\VendorSubscription\Model\Resource\Plan\CollectionFactory $planCollectionFactory,
        \Omnyfy\VendorSubscription\Model\SubscriptionFactory $subscriptionFactory,
        \Omnyfy\VendorSubscription\Model\Resource\Subscription\CollectionFactory $subscriptionCollectionFactory,
        \Magento\Framework\Pricing\PriceCurrencyInterface $currency,
        \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $roleCollectionFactory,
        \Omnyfy\Core\Helper\Queue $qHelper,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Omnyfy\VendorSubscription\Model\UsageFactory $usageFactory,
        \Omnyfy\VendorSubscription\Model\Resource\Update\CollectionFactory $updateCollectionFactory,
        \Omnyfy\VendorSubscription\Model\Resource\Update $updateResource
    ){
        $this->resource = $resource;
        $this->planFactory = $planFactory;
        $this->planResource = $planResource;
        $this->planCollectionFactory = $planCollectionFactory;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->subscriptionCollectionFactory = $subscriptionCollectionFactory;
        $this->currency = $currency;
        $this->roleCollectionFactory = $roleCollectionFactory;
        $this->_qHelper = $qHelper;
        $this->_vendorResource = $vendorResource;
        $this->usageFactory = $usageFactory;
        $this->updateCollectionFactory = $updateCollectionFactory;
        $this->updateResource = $updateResource;
        parent::__construct($context);
    }

    public function getRolePlanByVendorTypeId($vendorTypeId)
    {
        return $this->planResource->getRolePlanByVendorTypeId($vendorTypeId);
    }

    public function loadSubscriptionById($subscriptionId)
    {
        $subscription = $this->subscriptionFactory->create();
        $subscription->load($subscriptionId);

        if (!$subscription->getId()) {
            return false;
        }

        return $subscription;
    }

    public function loadPlanById($planId)
    {
        $plan = $this->planFactory->create();
        $plan->load($planId);

        if (!$plan->getId() || $planId !== $plan->getId()) {
            return false;
        }

        return $plan;
    }

    public function loadPlanByGatewayId($planGatewayId)
    {
        if (empty($planGatewayId)) {
            return false;
        }
        $collection = $this->planCollectionFactory->create();
        $collection->addFieldToFilter('gateway_id', $planGatewayId)
            ->setPageSize(1)
            ;
        $plan = $collection->getFirstItem();
        if (empty($plan->getId())) {
            return false;
        }

        return $plan;
    }

    public function loadSubscriptionByGatewayId($subGatewayId)
    {
        if (empty($subGatewayId)) {
            return false;
        }
        $collection = $this->subscriptionCollectionFactory->create();
        $collection->addFieldToFilter('gateway_id', $subGatewayId)
            ->setPageSize(1)
            ;
        $subscription = $collection->getFirstItem();
        if (empty($subscription->getId())) {
            return false;
        }

        return $subscription;
    }

    public function loadSubscriptionByEmail($email, $statuses)
    {
        if (empty($email)) {
            return false;
        }

        $collection = $this->subscriptionCollectionFactory->create();
        $collection->addFieldToFilter('vendor_email', $email)
            ->addFieldToFilter('status', ['in' => $statuses])
            ->setPageSize(1)
        ;
        $subscription = $collection->getFirstItem();
        if (empty($subscription->getId())) {
            return false;
        }

        return $subscription;
    }

    public function loadPendingUpdateByVendorId($vendorId)
    {
        if (empty($vendorId)) {
            return false;
        }
        $collection = $this->updateCollectionFactory->create();
        $collection->addVendorFilter($vendorId);
        $collection->addFieldToFilter('status', \Omnyfy\VendorSubscription\Model\Source\UpdateStatus::STATUS_PENDING)
            ->addOrder('created_at', 'DESC')
            ->setPageSize(1)
            ;

        $update = $collection->getFirstItem();
        if (empty($update->getId())) {
            return false;
        }

        return $update;
    }

    public function loadProcessingUpdate($subscriptionId)
    {
        if (empty($subscriptionId)) {
            return false;
        }
        $collection = $this->updateCollectionFactory->create();
        $collection->addFieldToFilter('subscription_id', $subscriptionId)
            ->addFieldToFilter('status', \Omnyfy\VendorSubscription\Model\Source\UpdateStatus::STATUS_PROCESSING)
            ->addOrder('created_at', 'DESC')
            ->setPageSize(1)
        ;

        $update = $collection->getFirstItem();
        if (empty($update->getId())) {
            return false;
        }

        return $update;
    }

    public function getRoleIdsMapByVendorTypeId($vendorTypeId) {
        return $this->planResource->getRoleIdMapByVendorTypeId($vendorTypeId);
    }

    public function getPlanIdsByVendorTypeId($vendorTypeId)
    {
        return $this->planResource->getPlanIdsByVendorTypeId($vendorTypeId);
    }

    public function formatPrice($amount, $includeContainer = true, $precision = 2)
    {
        return $this->currency->convertAndFormat($amount, $includeContainer, $precision);
    }

    public function getPlanIdRoleIdBySignUp($signUp)
    {
        if (empty($signUp) || empty($signUp->getId()) || !($signUp instanceof \Omnyfy\VendorSignUp\Model\SignUp)) {
            return false;
        }

        $extraInfo = $signUp->getExtraInfoAsArray();
        if (empty($extraInfo) || !array_key_exists('plan_role_id', $extraInfo)) {
            return false;
        }

        return explode('_', $extraInfo['plan_role_id']);
    }

    public function getRoleById($roleId)
    {
        $roleCollection = $this->roleCollectionFactory->create();
        $roleCollection->addFieldToFilter('role_id', $roleId);
        return $roleCollection->getFirstItem();
    }

    public function createSubscription($signUp, $vendor, $plan, $roleId, $token)
    {
        $extraInfo = [
            'sign_up_id' => $signUp->getId(),
            'card_token' => $token
        ];
        $data = [
            'vendor_id' => $vendor->getId(),
            'vendor_name' => $vendor->getName(),
            'vendor_email' => $vendor->getEmail(),
            'plan_id' => $plan->getId(),
            'plan_name' => $plan->getPlanName(),
            'plan_price' => $plan->getPrice(),
            'billing_interval' => $plan->getInterval(),
            'trial_days' => $plan->getTrialDays(),
            'status' => \Omnyfy\VendorSubscription\Model\Source\SubscriptionStatus::STATUS_PENDING_ACTIVE,
            'plan_gateway_id' => $plan->getGatewayId(),
            'vendor_type_id' => $vendor->getTypeId(),
            'role_id' => $roleId,
            'description' => 'Subscription for ' . $vendor->getName(),
            'extra_info' => json_encode($extraInfo),
        ];

        if ($plan->getIsFree()) {
            $data['gateway_id'] = 'FREE_SUB_' . $vendor->getId();
        }

        $sub = $this->subscriptionFactory->create();
        $sub->addData($data);
        $sub->save();

        //TODO: add subscription to queue
        $this->_qHelper->sendDataToQueue('subscription_init', ['subscription_id' => $sub->getId()]);
    }

    public function enableVendor($vendorId)
    {
        $this->_logger->debug('Try to enable vendor ' . $vendorId);
        $this->_vendorResource->updateVendorStatusById(
            $vendorId,
            \Omnyfy\Vendor\Api\Data\VendorInterface::STATUS_ENABLED
        );
    }

    public function disableVendor($vendorId)
    {
        $this->_logger->debug('Try to disable vendor ' . $vendorId);
        $this->_vendorResource->updateVendorStatusById(
            $vendorId,
            \Omnyfy\Vendor\Api\Data\VendorInterface::STATUS_DISABLED
        );
    }

    public function getUrl($route, $params = [])
    {
        return $this->_getUrl($route, $params);
    }

    public function getUpdatePlans($vendorTypeId, $excludePlanId)
    {
        $rolePlans = $this->getRolePlanByVendorTypeId($vendorTypeId);

        if (empty($rolePlans)) {
            return [];
        }

        $planIds = [];
        foreach($rolePlans as $rolePlan) {
            if ($rolePlan['plan_id'] == $excludePlanId) {
                continue;
            }
            $planIds[] = $rolePlan['plan_id'];
        }

        if (empty($planIds)) {
            return [];
        }

        $collection = $this->planCollectionFactory->create();
        $collection->addFieldToFilter('plan_id', ['in' => $planIds])
            ->addFieldToFilter('status', \Omnyfy\VendorSubscription\Model\Source\Status::STATUS_ACTIVE)
        ;

        $result = [];
        foreach($collection as $plan) {
            $result[] = $plan;
        }
        return $result;
    }

    public function updateVendorRole($vendorId, $roleId)
    {
        $vendorId = intval($vendorId);
        if (empty($vendorId)) {
            return false;
        }

        $conn = $this->resource->getConnection();
        $vendorTable = $conn->getTableName('omnyfy_vendor_vendor_entity');
        $select1 = $conn->select()
            ->from($vendorTable, ['email'])
            ->where('entity_id=?', $vendorId)
        ;

        $userTable = $conn->getTableName('admin_user');
        $select2 = $conn->select()
            ->from($userTable, ['user_id'])
            ->where('email in (?)', $select1)
        ;

        $roleTable = $conn->getTableName('authorization_role');

        $conn->update(
            $roleTable,
            ['parent_id' => $roleId],
            ['user_id IN (?)' => $select2]
        );

        return true;
    }

    public function saveUpdateStatus($id, $status)
    {
        $this->updateResource->updateById('status', $status, $id);
    }
}
 
