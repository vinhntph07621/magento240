<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-10
 * Time: 11:08
 */
namespace Omnyfy\VendorSubscription\Observer;

use Omnyfy\VendorSubscription\Model\Source\UpdateStatus;
use Omnyfy\VendorSubscription\Model\Source\SubscriptionStatus;

class SubscriptionUpdate implements \Magento\Framework\Event\ObserverInterface
{
    protected $helper;

    protected $emailHelper;

    protected $_logger;

    public function __construct(
        \Omnyfy\VendorSubscription\Helper\Data $helper,
        \Omnyfy\VendorSubscription\Helper\Email $emailHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->helper = $helper;
        $this->emailHelper = $emailHelper;
        $this->_logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $observer->getData('data');
        $subscription = $this->helper->loadSubscriptionByGatewayId($data['gateway_id']);

        // try to retrieve subscription by email and status
        if (empty($subscription)) {
            $subscription = $this->helper->loadSubscriptionByEmail(
                $data['email'],
                [\Omnyfy\VendorSubscription\Model\Source\SubscriptionStatus::STATUS_ACTIVE]
            );
        }

        if (empty($subscription)) {
            $this->_logger->error('Missing subscription', $data);
            return;
        }

        $origPlanGatewayId = $subscription->getPlanGatewayId();
        $origVendorTypeId = $subscription->getVendorTypeId();
        $origRoleId = $subscription->getRoleId();

        if ($data['plan_gateway_id'] != $origPlanGatewayId) {
            //load plan by gateway Id
            $plan = $this->helper->loadPlanByGatewayId($data['plan_gateway_id']);
            if (empty($plan)) {
                $this->_logger->error('Plan ' . $data['plan_gateway_id'] . ' does not exist');
                return;
            }
            //set plan_id, plan_price, billing_interval, role_id
            $planIdToRoleId = $this->helper->getRoleIdsMapByVendorTypeId($origVendorTypeId);
            if (!array_key_exists($plan->getId(), $planIdToRoleId)) {
                $this->_logger->error('Plan '. $plan->getId() . ' not assigned to vendor type ' . $origVendorTypeId);
                return;
            }

            $data['plan_id'] = $plan->getPlanId();
            $data['plan_name'] = $plan->getPlanName();
            $data['plan_price'] = $plan->getPrice();
            $data['billing_interval'] = $plan->getInterval();

            $toRoleId = $planIdToRoleId[$plan->getId()];
            if ($origRoleId != $toRoleId) {
                //change user role
                $this->helper->updateVendorRole($subscription->getVendorId(), $toRoleId);
            }
        }
        $toSendCancelEmail = false;
        if (intval($data['is_cancelled'])) {
            //mark subscription to cancel, wait for deleted trigger by gateway
            $data['status'] = SubscriptionStatus::STATUS_CANCELLED;
            if ($subscription->getStatus() !== SubscriptionStatus::STATUS_CANCELLED) {
               $toSendCancelEmail = true;
            }
        }
        $subscription->addData($data);
        $subscription->save();

        //UPDATE processing update
        $update = $this->helper->loadProcessingUpdate($subscription->getId());
        if (!empty($update)) {
            $this->helper->saveUpdateStatus($update->getId(), UpdateStatus::STATUS_DONE);
        }

        if ($toSendCancelEmail) {
            $this->emailHelper->sendCancelEmails($subscription);
        }
    }
}
 
