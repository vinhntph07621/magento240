<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-08
 * Time: 14:42
 */
namespace Omnyfy\VendorSubscription\Controller\Adminhtml\Plan;

class Save extends \Omnyfy\VendorSubscription\Controller\Adminhtml\AbstractAction
{
    const ADMIN_RESOURCE = 'Omnyfy_VendorSubscription::plans';

    protected $planFactory;

    protected $usageResource;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\VendorSubscription\Model\PlanFactory $planFactory,
        \Omnyfy\VendorSubscription\Model\Resource\Usage $usageResource
    )
    {
        $this->planFactory = $planFactory;
        $this->usageResource = $usageResource;
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
            $plan = $this->planFactory->create();

            $inputFilter = new \Zend_Filter_Input([], [], $data);

            $data = $inputFilter->getUnescaped();

            if (empty($id) && isset($data['plan_id'])) {
                $id = intval($data['plan_id']);
            }

            if ($id) {
                $plan->load($id);
                if ($id != $plan->getId()) {
                    throw new \Magento\Framework\Exception\LocalizedException(__('The wrong plan is specified.'));
                }
            }
            $data['gateway_id'] = trim($data['gateway_id']);
            $plan->addData($data);

            $this->_session->setPageData($plan->getData());
            $plan->save();

            //Save plan-usage relation
            if (!empty($plan->getId()) && isset($data['plan_usage'])) {
                $this->usageResource->savePlanUsage($plan->getId(), $data['plan_usage']);
            }

            $this->messageManager->addSuccessMessage(__('You saved the subscription plan'));
            $this->_session->setPageData(false);

            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('omnyfy_subscription/*/edit', ['id' => $plan->getId()]);
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
 