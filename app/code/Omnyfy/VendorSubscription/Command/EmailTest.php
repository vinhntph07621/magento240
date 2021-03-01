<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 3/10/19
 * Time: 12:05 pm
 */
namespace Omnyfy\VendorSubscription\Command;

use Omnyfy\Core\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class EmailTest extends Command
{
    protected $appState;

    protected $helper;

    protected $subscriptionFactory;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Omnyfy\VendorSubscription\Helper\Email $helper,
        \Omnyfy\VendorSubscription\Model\SubscriptionFactory $subscriptionFactory,
        $name = null
    ) {
        $this->appState = $state;
        $this->helper = $helper;
        $this->subscriptionFactory = $subscriptionFactory;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('omnyfy:subscription:test_email');
        $this->setDescription('Test email');
        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->lock()) {
            $output->writeln('Previous instance of this command still running.');
            return;
        }
        try {
            $code = $this->appState->getAreaCode();
            $output->writeln('Running in area: ' . $code);
        } catch (\Exception $e) {
            $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        }

        try {
            $output->writeln('Test Email sending');

            $subscription = $this->subscriptionFactory->create();
            $subscription->load(16);
            $this->helper->sendCancelEmails($subscription);
        }
        catch(\Exception $e)
        {
            $output->writeln($e->getMessage());
        }
        $output->writeln('Done');
    }
}
 