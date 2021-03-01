<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-08-08
 * Time: 15:15
 */
namespace Omnyfy\VendorSubscription\Command;


use Omnyfy\Core\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Init extends Command
{
    protected $_appState;

    protected $_storeManager;

    protected $_qHelper;

    protected $_helper;

    protected $_gwHelper;

    protected $_eventManager;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\Core\Helper\Queue $queueHelper,
        \Omnyfy\VendorSubscription\Helper\Data $helper,
        \Omnyfy\VendorSubscription\Helper\GatewayInterface $gwHelper,
        \Magento\Framework\Event\Manager $eventManager,
        string $name = null)
    {
        $this->_appState = $state;
        $this->_storeManager = $storeManager;
        $this->_qHelper = $queueHelper;
        $this->_helper = $helper;
        $this->_gwHelper = $gwHelper;
        $this->_eventManager = $eventManager;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('omnyfy:subscription:init');
        $this->setDescription('Initial process for subscription');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('Previous instance of this command still running.');
            return;
        }
        try {
            $code = $this->_appState->getAreaCode();
            $output->writeln('Running in area: ' . $code);
        } catch (\Exception $e) {
            $this->_appState->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        }

        $output->writeln('Start to process');

        $i = $done = $failed = $invalid = 0;
        $itemId = null;
        while($data = $this->_qHelper->takeDataFromQueue('subscription_init', $itemId)) {
            $i++;
            if (!isset($data['subscription_id'])) {
                $invalid++;
                continue;
            }
            $subscription = $this->_helper->loadSubscriptionById($data['subscription_id']);
            if (empty($subscription)) {
                $invalid++;
                continue;
            }

            $plan = $this->_helper->loadPlanById($subscription->getPlaneId());
            if (empty($plan)) {
                $invalid++;
                continue;
            }

            //if plan is free, dispatch events instead of sending to gateway
            if (intval($plan->getIsFree())) {
                try {
                    $data = [
                        'plan_gateway_id' => $subscription->getPlanGatewayId(),
                        'sub_gateway_id' => 'FREE_SUB_'. $subscription->getVendorId(),
                        'billing_date' => date('Y-m-d H:i:s'),
                        'start_date' => date('Y-m-d H:i:s'),
                        'end_date' => null,
                        'billing_account_name' => $subscription->getVendorName(),
                        'billing_amount' => 0.0,
                        'status' => \Omnyfy\VendorSubscription\Model\Source\HistoryStatus::STATUS_SUCCESS,
                        'invoice_link' => null
                    ];

                    $this->_eventManager->dispatch('omnyfy_subscription_invoice_succeeded',
                        [
                            'data' => $data
                        ]
                    );

                    $gatewaySubscription = true;
                }
                catch(\Exception $e) {
                    $output->writeln($e->getMessage());
                    $gatewaySubscription = false;
                }

            }
            else{
                $gatewaySubscription = $this->sendSubscription($subscription);
            }

            if (empty($gatewaySubscription)) {
                $this->_qHelper->updateQueueMsgStatus($itemId, 'failed');
                $failed++;
            }
            else {
                $this->_qHelper->updateQueueMsgStatus($itemId, 'done');
                $done++;
            }
        }

        $output->writeln('Done. Got '. $i . ' items in total.');
        $output->writeln('Invalid items: '. $invalid);
        $output->writeln('Succeeded: '. $done);
        $output->writeln('Failed: ' . $failed);
    }

    protected function sendSubscription($subscription)
    {
        try {
            $extraInfo = json_decode($subscription->getExtraInfo(), true);

            if (empty($extraInfo)) {
                return false;
            }

            if (!array_key_exists('card_token', $extraInfo)) {
                return false;
            }

            $customer = $this->_gwHelper->searchCreateCustomer($subscription->getVendorEmail(), $extraInfo['card_token']);
            if (empty($customer)) {
                return false;
            }

            $gwPlan = $this->_gwHelper->retrievePlan($subscription->getPlanGatewayId());
            if (empty($gwPlan)) {
                return false;
            }

            $gwSub = $this->_gwHelper->searchCreateSubscription($customer['id'], $gwPlan['id'], $subscription->getTrialDays());
            if (empty($gwSub) || !array_key_exists('id', $gwSub)) {
                return false;
            }

            $subscription->getResource()->updateById('gateway_id', $gwSub['id'], $subscription->getId());
            $subscription->getResource()->updateById('customer_gateway_id', $gwSub['customer'], $subscription->getId());

            return $gwSub;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }
}
 