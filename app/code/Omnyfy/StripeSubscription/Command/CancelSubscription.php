<?php
/**
 * Project: Stripe Subscription
 * User: jing
 * Date: 2019-08-10
 * Time: 12:09
 */
namespace Omnyfy\StripeSubscription\Command;

use Omnyfy\Core\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CancelSubscription extends Command
{
    protected $_appState;

    protected $_helper;

    protected $_qHelper;

    protected $subHelper;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Omnyfy\StripeSubscription\Helper\Data $_helper,
        \Omnyfy\Core\Helper\Queue $_qHelper,
        \Omnyfy\VendorSubscription\Helper\Data $subHelper,
        $name = null
    ) {
        $this->_appState = $state;
        $this->_helper = $_helper;
        $this->_qHelper = $_qHelper;
        $this->subHelper = $subHelper;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('omnyfy:stripe:subscription_cancel');
        $this->setDescription('Process subscription delete event');

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
            $this->_appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        }
        $output->writeln('Start to process');

        $i = $done = $failed = $invalid = 0;

        $itemId = null;
        while($qItem = $this->_qHelper->takeDataFromQueue('subscription_cancel', $itemId)) {
            $i++;
            $subscriptionId = $qItem['subscription_id'];

            $subscription = $this->subHelper->loadSubscriptionById($subscriptionId);
            if (empty($subscription)) {
                $this->_qHelper->updateQueueMsgStatus($itemId, 'blocking');
                $invalid++;
                continue;
            }

            try {
                //send to gateway only, leave response process to webhook
                $data = [
                    'cancel_at_period_end' => 'true'
                ];
                $this->_helper->updateSubscription($subscription->getGatewayId(), $data);

                $this->_qHelper->updateQueueMsgStatus($itemId, 'done');
                $done++;
            }
            catch (\Exception $e) {
                $output->writeln($e->getMessage());
                $this->_qHelper->updateQueueMsgStatus($itemId, 'error');
                $failed++;
            }
        }

        $output->writeln('Done. Got '. $i . ' items in total.');
        $output->writeln('Invalid items: '. $invalid);
        $output->writeln('Succeeded: '. $done);
        $output->writeln('Failed: ' . $failed);
    }
}
 