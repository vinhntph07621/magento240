<?php
/**
 * Project: Strip Subscription
 * User: jing
 * Date: 2019-07-18
 * Time: 14:08
 */
namespace Omnyfy\StripeSubscription\Command;

use Omnyfy\Core\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Callbacks extends Command
{
    protected $_appState;

    protected $_storeManager;

    protected $_qHelper;

    protected $_helper;

    protected $_requiredCallbacks = [
        /*
        [
            'description' => 'Subscription Created',
            'object_type' => 'subscription',
            'enabled_events' => ['custoemr.subscription.created'],
            'url' => 'omnyfy_stripe/subscription/created',
            'status' => 'enabled',
        ],
        */
        [
            'description' => 'Subscription Deleted',
            'object_type' => 'subscription',
            'enabled_events' => ['customer.subscription.deleted'],
            'url' => 'omnyfy_stripe/subscription/deleted',
            'status' => 'enabled',
        ],
        [
            'description' => 'Subscription Updated',
            'object_type' => 'subscription',
            'enabled_events' => ['customer.subscription.updated'],
            'url' => 'omnyfy_stripe/subscription/updated',
            'status' => 'enabled',
        ],
        [
            'description' => 'Subscription Created',
            'object_type' => 'subscription',
            'enabled_events' => ['customer.subscription.created'],
            'url' => 'omnyfy_stripe/subscription/created',
            'status' => 'enabled',
        ],
        [
            'description' => 'Invoice Payment Failed',
            'object_type' => 'invoice',
            'enabled_events' => ['invoice.payment_failed'],
            'url' => 'omnyfy_stripe/invoice_payment/failed',
            'status' => 'enabled',
        ],
        [
            'description' => 'Invoice Payment Succeed',
            'object_type' => 'invoice',
            'enabled_events' => ['invoice.payment_succeeded'],
            'url' => 'omnyfy_stripe/invoice_payment/succeed',
            'status' => 'enabled',
        ],
    ];

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\Core\Helper\Queue $queueHelper,
        \Omnyfy\StripeSubscription\Helper\Data $helper,
        string $name = null)
    {
        $this->_appState = $state;
        $this->_storeManager = $storeManager;
        $this->_qHelper = $queueHelper;
        $this->_helper = $helper;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('omnyfy:stripe:callbacks');
        $this->setDescription('Setup script for callbacks');

        $this->addArgument('setup', InputArgument::OPTIONAL, 'Setup');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('Previous instance of this command still running.');
            return;
        }
        try{
            $code = $this->_appState->getAreaCode();
            $output->writeln('Running in area: ' . $code);
        }
        catch(\Exception $e) {
            $this->_appState->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        }

        $setup = $input->getArgument('setup');
        if (!empty($setup)) {
            //get list of callbacks, and loop to display the object_type, url and enabled
            $callbacks = $this->listCallbacks();
            $callbacks = empty($callbacks) ? [] : $callbacks;
            $store = $this->_storeManager->getStore();
            foreach($this->_requiredCallbacks as $required) {
                $url = $store->getUrl($required['url']);
                $found = false;
                foreach($callbacks as $callback) {
                    if ($required['enabled_events'] !== $callback['enabled_events']) {
                        continue;
                    }
                    $found = true;
                    if ($callback['url'] !== $url || $callback['status'] !== $required['status']) {
                        //UPDATE callbacks
                        $output->writeln('Update callback for '. $callback['enabled_events'][0]);
                        $this->_helper->updateWebhook($callback['id'], $url, $required['enabled_events']);
                    }
                }
                if (!$found) {
                    //CREATE callbacks
                    $output->writeln('Create callback for '. $required['enabled_events'][0]);
                    $this->_helper->createWebhook($url, $required['enabled_events']);
                }
            }
        }

        //CALL list again
        $callbacks = $this->listCallbacks();
        $callbacks = empty($callbacks) ? [] : $callbacks;
        $i = 1;
        foreach($callbacks as $callback) {
            $output->writeln(
                $i .', ' .
                $callback['id']. ', "' .
                implode(' ', $callback['enabled_events']) . '", ' .
                $callback['url'] . ', ' .
                $callback['status']
            );
            $i++;
        }

        return;
    }

    protected function listCallbacks()
    {
        return $this->_helper->listWebhooks();
    }

}
 