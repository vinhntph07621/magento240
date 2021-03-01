<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-09
 * Time: 15:12
 */
namespace Omnyfy\VendorSubscription\Observer;

class SubscriptionInvoiceSuccess implements \Magento\Framework\Event\ObserverInterface
{
    protected $historyResource;

    protected $helper;

    protected $signUpFactory;

    protected $signUpHelper;

    protected $_backendUrl;

    protected $_logger;

    protected $usageHelper;

    public function __construct(
        \Omnyfy\VendorSubscription\Model\Resource\History $historyResource,
        \Omnyfy\VendorSubscription\Helper\Data $helper,
        \Omnyfy\VendorSignUp\Model\SignUpFactory $signUpFactory,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Omnyfy\VendorSignUp\Helper\Data $signUpHelper,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\VendorSubscription\Helper\Usage $usageHelper
    )
    {
        $this->historyResource = $historyResource;
        $this->helper = $helper;
        $this->signUpFactory = $signUpFactory;
        $this->_backendUrl = $backendUrl;
        $this->signUpHelper = $signUpHelper;
        $this->_logger = $logger;
        $this->usageHelper = $usageHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $data = $observer->getData('data');

        //load subscription by sub_gateway_id
        $subscription = $this->helper->loadSubscriptionByGatewayId($data['sub_gateway_id']);

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

        $plan = $this->helper->loadPlanById($subscription->getPlanId());
        if (empty($plan)) {
            $this->_logger->error('Missing plan', $data);
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

        unset($data['email']);
        unset($data['plan_gateway_id']);

        //depends on status from gateway
        $subscription->setStatus($data['status']);
        $subscription->save();

        //save success history
        $data['plan_id'] = $plan->getId();
        $data['vendor_id'] = $subscription->getVendorId();
        $data['vendor_name'] = $subscription->getVendorName();
        $data['subscription_id'] = $subscription->getId();
        $data['plan_price'] = $plan->getPrice();
        $data['plan_name'] = $plan->getPlanName();
        $this->historyResource->bulkSave([$data]);

        //If multi account per vendor enabled, refactoring enableVendor logic in the helper
        //enable vendor event it's already enabled
        $this->helper->enableVendor($subscription->getVendorId());

        //email sending for
        $extraInfo = $subscription->getExtraInfoAsArray();
        if (empty($extraInfo) || !array_key_exists('sign_up_id', $extraInfo)) {
            return;
        }

        //assign usage to vendor for one off resources
        $this->usageHelper->assignInitUsage($subscription->getVendorId(), $plan);

        //repeat usage assign
        $this->usageHelper->assignRepeatPlanUsage($subscription->getVendorId(), $plan, $data['start_date'], $data['end_date']);

        //load sign up by sign_up_id
        $signUp = $this->signUpFactory->create();
        $signUp->load($extraInfo['sign_up_id']);
        if (empty($signUp->getId()) || $signUp->getId() != $extraInfo['sign_up_id']) {
            return;
        }
        //check email_sent flag on signUp model, send email to new signUp
        if (!$signUp->getEmailSent()) {
            $to = [
                'email' => trim($signUp->getEmail()),
                'name' => $signUp->getBusinessName()
            ];

            $url = $this->_backendUrl->getRouteUrl('adminhtml');
            $vars = [
                'businessname' => $signUp->getBusinessName(),
                'admin_login_link' => $url,
                'admin_forgot_password' => $url . 'auth/forgotpassword'
            ];

            $this->signUpHelper->sendSignUpApproveToCustomer($vars, $to);
            $signUp->getResource()->updateBindsById(['email_sent' => 1], $signUp->getId());
        }
    }
}
 