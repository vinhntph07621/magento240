<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 2/8/17
 * Time: 9:44 AM
 */
namespace Omnyfy\Vendor\Command;

use Omnyfy\Core\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateOrderJson extends Command
{
    protected $appState;

    protected $queueHelper;

    protected $vendorHelper;

    protected $orderHelper;

    const NAME_ARGUMENT = "entity_id";
    const NAME_OPTION = "option";

    public function __construct(
        \Magento\Framework\App\State $state,
        \Omnyfy\Core\Helper\Queue $queueHelper,
        \Omnyfy\Vendor\Helper\Data $vendorHelper,
        \Omnyfy\Vendor\Helper\Order $orderHelper

    )
    {
        $this->appState = $state;
        $this->queueHelper = $queueHelper;
        $this->vendorHelper = $vendorHelper;
        $this->orderHelper = $orderHelper;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('omnyfy:vendor:generate_order_json');
        $this->setDescription('Generate Order JSON for fuse');
        $this->setDefinition([
            new InputArgument(self::NAME_ARGUMENT, InputArgument::OPTIONAL, "Name"),
            new InputOption(self::NAME_OPTION, "-a", InputOption::VALUE_NONE, "Option functionality")
        ]);
        parent::configure();
    }

    protected function execute(
        InputInterface $input,
        OutputInterface $output
    )
    {
        if (!$this->lock()) {
            return;
        }

        try{
            $code = $this->appState->getAreaCode();
        }
        catch(\Exception $e) {
            $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
        }

        $orderId = $input->getArgument(self::NAME_ARGUMENT);

        $output->writeln('Start to process');
        $orderJson = $this->orderHelper->getFuseJson($orderId);
        $output->writeln($orderJson);
        $output->writeln('End process');
    }
}