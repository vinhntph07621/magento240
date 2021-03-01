<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-08
 * Time: 17:35
 */
namespace Omnyfy\VendorSubscription\Controller\Adminhtml\Subscription;

class Edit extends \Omnyfy\VendorSubscription\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_VendorSubscription::subscriptions';

    protected $subscriptionFactory;

    protected $coreRegistry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Registry $coreRegistry,
        \Omnyfy\VendorSubscription\Model\SubscriptionFactory $subscriptionFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->subscriptionFactory = $subscriptionFactory;
        parent::__construct($context, $resultPageFactory, $logger);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $subscription = $this->loadSubscription($this->getRequest());

        if ($id && !$subscription->getId()) {
            $this->messageManager->addErrorMessage(__('This subscription no longer exists.'));
            return $this->resultRedirectFactory->create()->setPath('omnyfy_subscription/*');
        }

        $this->_eventManager->dispatch('omnyfy_subscription_subscription_edit_action', ['subscription' => $subscription]);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_VendorSubscription::subscriptions');
        $resultPage->getConfig()->getTitle()->prepend(__('Subscription'));
        $resultPage->getConfig()->getTitle()->prepend($subscription->getVendorName());

        $resultPage->getLayout()->getBlock('omnyfy_subscription_subscription_edit');

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
 