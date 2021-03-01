<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 12/9/19
 * Time: 11:41 pm
 */
namespace Omnyfy\VendorSubscription\Controller\Adminhtml\Subscription\Update;

use Omnyfy\VendorSubscription\Model\Source\UpdateStatus;

class Save extends \Omnyfy\VendorSubscription\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_VendorSubscription::subscription_update';

    protected $coreRegistry;

    protected $_gwHelper;

    protected $updateFactory;

    protected $dataHelper;

    protected $subscriptionFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Registry $coreRegistry,
        \Omnyfy\VendorSubscription\Helper\GatewayInterface $_gwHelper,
        \Omnyfy\VendorSubscription\Model\UpdateFactory $updateFactory,
        \Omnyfy\VendorSubscription\Helper\Data $dataHelper,
        \Omnyfy\VendorSubscription\Model\SubscriptionFactory $subscriptionFactory
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->_gwHelper = $_gwHelper;
        $this->updateFactory = $updateFactory;
        $this->dataHelper = $dataHelper;
        $this->subscriptionFactory = $subscriptionFactory;
        parent::__construct($context, $resultPageFactory, $logger);
    }

    public function execute()
    {
        //validation
        $id = $this->getRequest()->getParam('id');
        $subscription = $this->loadSubscription($this->getRequest());
        if (empty($id) || empty($subscription)) {
            $this->messageManager->addErrorMessage('Wrong data provided');
            return $this->resultRedirectFactory->create()->setPath('omnyfy_vendor/vendor/index');
        }

        $vendorId = $subscription->getVendorId();

        try{
            $data = $this->getRequest()->getPostValue();
            $inputFilter = new \Zend_Filter_Input([], [], $data);
            $data = $inputFilter->getUnescaped();

            if (!isset($data['to_plan_id']) || empty($data['to_plan_id'])) {
                $this->messageManager->addErrorMessage('Something wrong with form submit.');
                return $this->resultRedirectFactory->create()->setPath('omnyfy_subscription/subscription_update/edit', ['id' => $id]);
            }

            $toPlanId = $data['to_plan_id'];

            //check if toPlan assigned to this vendor type
            $planIdToRoleId = $this->dataHelper->getRoleIdsMapByVendorTypeId($subscription->getVendorTypeId());
            if (!array_key_exists($toPlanId, $planIdToRoleId)) {
                $this->messageManager->addErrorMessage('Selected plan is not for vendor type '. $subscription->getVendorTypeId());
                return $this->resultRedirectFactory->create()->setPath('omnyfy_subscription/subscription_update/edit', ['id' => $id]);
            }

            $fromPlan = $this->dataHelper->loadPlanById($subscription->getPlanId());
            $toPlan = $this->dataHelper->loadPlanById($data['to_plan_id']);

            if (empty($fromPlan) || empty($toPlan)) {
                $this->messageManager->addErrorMessage('Related plan does not exist any more');
                return $this->resultRedirectFactory->create()->setPath('omnyfy_subscription/subscription_update/edit', ['id' => $id]);
            }

            //TODO: Implement upgrade and downgrade from free to paid and paid to free.
            if (($fromPlan->getIsFree() && !$toPlan->getIsFree())
                || (!$fromPlan->getIsFree() && $toPlan->getIsFree())) {
                $this->messageManager->addErrorMessage('Upgrade from/to a free plan not supported yet.');
                return $this->resultRedirectFactory->create()->setPath('omnyfy_subscription/subscription_update/edit', ['id' => $id]);
            }

            $update = $this->updateFactory->create();

            //save subscription update model
            if (empty($update->getId())) {
                $updateData = [
                    'vendor_id' => $vendorId,
                    'subscription_id' => $subscription->getId(),
                    'from_plan_id' => $fromPlan->getId(),
                    'from_plan_name' => $fromPlan->getPlanName(),
                    'to_plan_id' => $toPlan->getId(),
                    'to_plan_name' => $toPlan->getPlanName(),
                    'status' => UpdateStatus::STATUS_PENDING
                ];
                $update->addData($updateData);
                $update->save();
            }

            $result = 1;
            //send request when pending or failed
            if (UpdateStatus::STATUS_DONE != $update->getStatus()) {
                $result = $this->_gwHelper->changePlan(
                    $subscription->getGatewayId(),
                    $fromPlan->getGatewayId(),
                    $toPlan->getGatewayId()
                );
            }

            if (empty($result)) {
                $this->dataHelper->saveUpdateStatus($update->getId(), UpdateStatus::STATUS_FAILED);
                $this->messageManager->addErrorMessage('Failed to change plan');
            }
            else {
                $this->dataHelper->saveUpdateStatus($update->getId(), UpdateStatus::STATUS_DONE);
                $this->messageManager->addSuccessMessage('You subscription updated to the plan you selected');
            }

            return $this->resultRedirectFactory->create()->setPath('omnyfy_vendor/vendor/edit', ['id' => $vendorId]);
        }
        catch(\Exception $e) {
            $this->_log->debug($e->getMessage());
            $this->messageManager->addErrorMessage('');
            return $this->_redirect('omnyfy_subscription/subscription_update/edit', ['id' => $id]);
        }
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
 