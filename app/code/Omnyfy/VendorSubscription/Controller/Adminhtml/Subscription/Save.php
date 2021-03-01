<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-08
 * Time: 17:42
 */
namespace Omnyfy\VendorSubscription\Controller\Adminhtml\Subscription;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Omnyfy\VendorSubscription\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_VendorSubscription::subscriptions';

    protected $subscriptionFactory;

    protected $vendorRepository;

    protected $planFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\VendorSubscription\Model\SubscriptionFactory $subscriptionFactory,
        \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository,
        \Omnyfy\VendorSubscription\Model\PlanFactory $planFactory
    ) {
        $this->subscriptionFactory = $subscriptionFactory;
        $this->vendorRepository = $vendorRepository;
        $this->planFactory = $planFactory;
        parent::__construct($context, $resultPageFactory, $logger);
    }

    public function execute()
    {
        if (! $this->getRequest()->getPostValue()) {
            $this->_redirect('omnyfy_subscription/*/');
            return;
        }

        $data = $this->getRequest()->getPostValue();
        $id = $this->getRequest()->getParam('id', null);
        $p = empty($id) ? [] : ['id' => $id];

        try {
            $subscription = $this->subscriptionFactory->create();

            $inputFilter = new \Zend_Filter_Input([], [], $data);

            $data = $inputFilter->getUnescaped();

            if (empty($id) && isset($data['id'])) {
                $id = intval($data['id']);
            }

            if ($id) {
                $subscription->load($id);
                if ($id != $subscription->getId()) {
                    throw new LocalizedException(__('The wrong plan is specified.'));
                }
            }
            else{
                unset($data['id']);
            }

            //TODO: validate vendor plan
            $vendorId = intval($data['vendor_id']);
            $vendor = $this->vendorRepository->getById($vendorId);
            if (empty($vendor)) {
                throw new LocalizedException(__('Vendor not exist any more'));
            }

            if (empty($vendor->getEmail())) {
                throw new LocalizedException(__('Email for Vendor is missing'));
            }

            $planId = intval($data['plan_id']);
            $plan = $this->planFactory->create();
            $plan->load($planId);

            if (!$plan->getId()) {
                throw new LocalizedException(__('Plan not exist any more'));
            }

            $rolePlans = $plan->getResource()->getRolePlanByVendorTypeId($vendor->getTypeId());
            if (empty($rolePlans)) {
                throw new LocalizedException(__('No role and plan assigned to this vendor type %1', $vendor->getTypeId()));
            }
            $planIds = [];
            foreach($rolePlans as $row) {
                $planIds[] = $row['plan_id'];
            }

            if (!in_array($planId, $planIds)) {
                throw new LocalizedException(__('Plan %1 not been assign to vendor type %2.', $plan->getPlanName(), $vendor->getTypeId()));
            }

            $data['vendor_email'] = $vendor->getEmail();
            $data['plan_name'] = $plan->getPlanName();
            $data['plan_price'] = $plan->getPrice();
            $data['billing_interval'] = $plan->getInterval();
            $data['plan_gateway_id'] = $plan->getGatewayId();
            foreach($rolePlans as $row) {
                if ($planId == $row['plan_id']) {
                    $data['role_id'] = $row['role_id'];
                    break;
                }
            }

            $subscription->addData($data);

            $this->_session->setPageData($subscription->getData());
            $subscription->save();

            $this->messageManager->addSuccessMessage(__('You saved the subscription'));
            $this->_session->setPageData(false);

            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('omnyfy_subscription/*/edit', ['id' => $subscription->getId()]);
                return;
            }
            $this->_redirect('omnyfy_subscription/*/');
            return;
        }
        catch(\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            if (!empty($id)) {
                $this->_redirect('omnyfy_subscription/*/edit', $p);
            } else {
                $this->_redirect('omnyfy_subscription/*/new');
            }
            return;
        }
        catch(\Exception $e1) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while saving the plan data. Please review the error log.')
            );
            $this->_log->critical($e1);
            $this->_session->setPageData($data);

            if (!empty($id)) {
                $this->_redirect('omnyfy_subscription/*/edit', $p);
            } else {
                $this->_redirect('omnyfy_subscription/*/new');
            }
            return;
        }
    }
}
 