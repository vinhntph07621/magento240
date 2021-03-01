<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 10/9/19
 * Time: 5:16 pm
 */
namespace Omnyfy\VendorSubscription\Controller\Adminhtml\Subscription\Update;

class Edit extends \Omnyfy\VendorSubscription\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_VendorSubscription::subscription_update';

    protected $subscriptionFactory;

    protected $updateFactory;

    protected $coreRegistry;

    protected $dataHelper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Registry $coreRegistry,
        \Omnyfy\VendorSubscription\Model\SubscriptionFactory $subscriptionFactory,
        \Omnyfy\VendorSubscription\Model\UpdateFactory $updateFactory,
        \Omnyfy\VendorSubscription\Helper\Data $dataHelper
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->updateFactory = $updateFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct($context, $resultPageFactory, $logger);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $subscription = $this->loadSubscription($this->getRequest());

        if ($id && !$subscription->getId()) {
            $this->messageManager->addErrorMessage('This subscription no longer exists.');
            return $this->resultRedirectFactory->create()->setPath('omnyfy_vendor/*');
        }

        //TODO: check if plan of this subscription is the only option for this vendor type,
        //If so, redirect back to vendor edit page
        $allowedPlans = $this->dataHelper->getUpdatePlans($subscription->getVendorTypeId(), $subscription->getPlaneId());
        if (empty($allowedPlans)) {
            $this->messageManager->addErrorMessage('There is no other plan for your subscription.');
            return $this->resultRedirectFactory->create()
                ->setPath('omnyfy_vendor/vendor/edit', ['id' => $subscription->getVendorId()]);
        }

        $update = $this->dataHelper->loadPendingUpdateByVendorId($subscription->getVendorId());
        $update = empty($update) ? $this->updateFactory->create() : $update;
        $update->setVendorId($subscription->getVendorId());
        $update->setSubscriptionId($subscription->getId());
        $update->setFromPlanId($subscription->getPlaneId());
        $update->setFromPlanName($subscription->getPlaneName());

        $this->coreRegistry->register('current_omnyfy_subscription_update', $update);

        $this->_eventManager->dispatch('omnyfy_subscription_subscription_update_edit_action',
            [
                'subscription' => $subscription,
                'update' => $update
            ]
        );

        $resultPage = $this->resultPageFactory->create();
        //$resultPage->setActiveMenu('Omnyfy_VendorSubscription::subscriptions');
        //$resultPage->getConfig()->getTitle()->prepend(__('Subscription'));
        //$resultPage->getConfig()->getTitle()->prepend($subscription->getVendorName());

        $resultPage->getLayout()->getBlock('omnyfy_subscription_subscription_update_edit');

        return $resultPage;
    }

    protected function loadSubscription($request)
    {
        $subscriptionId = intval($request->getParam('id'));

        $subscription = $this->subscriptionFactory->create();
        if ($subscriptionId) {
            try {
                $subscription->load($subscriptionId);
            }
            catch (\Exception $e) {
                $this->_log->critical($e);
            }
        }

        $this->coreRegistry->register('current_omnyfy_subscription_subscription', $subscription);
        return $subscription;
    }
}
 