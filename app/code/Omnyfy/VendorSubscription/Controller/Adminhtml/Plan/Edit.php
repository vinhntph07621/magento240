<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-05
 * Time: 12:43
 */
namespace Omnyfy\VendorSubscription\Controller\Adminhtml\Plan;

class Edit extends \Omnyfy\VendorSubscription\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_VendorSubscription::plans';

    protected $planFactory;

    protected $_coreRegistry;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Registry $coreRegistry,
        \Omnyfy\VendorSubscription\Model\PlanFactory $planFactory
    ) {
        $this->planFactory = $planFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $resultPageFactory, $logger);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $plan = $this->loadPlan($this->getRequest());

        if ($id && !$plan->getId()) {
            $this->messageManager->addErrorMessage(__('This plan no longer exists.'));
            return $this->resultRedirectFactory->create()->setPath('omnyfy_subscription/*');
        }

        $this->_eventManager->dispatch('omnyfy_subscription_plan_edit_action', ['plan' => $plan]);

        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Omnyfy_VendorSubscription::plans');
        $resultPage->getConfig()->getTitle()->prepend(__('Plan'));
        $resultPage->getConfig()->getTitle()->prepend($plan->getPlanName());

        $resultPage->getLayout()->getBlock('omnyfy_subscription_plan_edit');

        return $resultPage;
    }

    protected function loadPlan($request)
    {
        $planId = intval($request->getParam('id'));

        $plan = $this->planFactory->create();
        if ($planId) {
            try {
                $plan->load($planId);
            }
            catch (\Exception $e) {
                $this->_log->critical($e);
            }
        }

        $this->_coreRegistry->register('current_omnyfy_subscription_plan', $plan);
        return $plan;
    }
}
 