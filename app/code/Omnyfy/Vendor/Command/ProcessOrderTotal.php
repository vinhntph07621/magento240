<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 2/8/17
 * Time: 9:44 AM
 */
namespace Omnyfy\Vendor\Command;

use Omnyfy\Core\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessOrderTotal extends Command
{
    protected $appState;

    protected $queueHelper;

    protected $vendorHelper;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Omnyfy\Core\Helper\Queue $queueHelper,
        \Omnyfy\Vendor\Helper\Data $vendorHelper
    )
    {
        $this->appState = $state;
        $this->queueHelper = $queueHelper;
        $this->vendorHelper = $vendorHelper;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('omnyfy:vendor:order_total');
        $this->setDescription('Process Vendor Order Total');
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

        $output->writeln('Start to process');

        $i = $done = $failed = $invalid = 0;

        while($qItem = $this->queueHelper->takeMsgFromQueue('vendor_order_total')) {
            $i++;
            if (!isset($qItem['id']) || empty($qItem['id'])) {
                $output->writeln('Got an item without id at '. $i);
                $invalid ++;
                continue;
            }
            if (!isset($qItem['message']) || empty($qItem['message'])) {
                $this->queueHelper->updateQueueMsgStatus($qItem['id'], 'blocking');
                $invalid ++;
                continue;
            }
            $itemData = json_decode($qItem['message'], true);
            if (empty($itemData) || !isset($itemData['order_id'])) {
                $this->queueHelper->updateQueueMsgStatus($qItem['id'], 'blocking');
                $invalid++;
                continue;
            }
            $orderId = $itemData['order_id'];

            $result = $this->vendorHelper->calculateVendorOrderTotal($orderId);

            if ($result) {
                $this->queueHelper->updateQueueMsgStatus($qItem['id'], 'done');
                $done ++;
            }
            else{
                $this->queueHelper->updateQueueMsgStatus($qItem['id'], 'failed');
                $failed++;
            }
        }
        $output->writeln('Done. Got '. $i . ' items in total.');
        $output->writeln('Invalid items: '. $invalid);
        $output->writeln('Succeeded: '. $done);
        $output->writeln('Failed: ' . $failed);
    }
}