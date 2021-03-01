<?php
/**
 * Project: Mcm.
 * User: jing
 * Date: 2018-12-20
 * Time: 15:02
 */

namespace Omnyfy\Mcm\Command;

use Omnyfy\Core\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessPayoutQueue extends Command
{
    protected $appState;

    protected $queueHelper;

    protected $eventManager;

    protected $shipmentRepository;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Omnyfy\Core\Helper\Queue $queueHelper,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
    )
    {
        $this->appState = $state;
        $this->queueHelper = $queueHelper;
        $this->eventManager = $eventManager;
        $this->shipmentRepository = $shipmentRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('omnyfy:mcm:process_payout_queue');
        $this->setDescription('Process Payout Order Queue');
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

        while($qItem = $this->queueHelper->takeMsgFromQueue('mcm_payout_order')) {
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
            if (empty($itemData) || !isset($itemData['shipment_id'])) {
                $this->queueHelper->updateQueueMsgStatus($qItem['id'], 'blocking');
                $invalid++;
                continue;
            }
            $shipmentId = $itemData['shipment_id'];

            try {
                //Load shipment by id
                $shipment = $this->shipmentRepository->get($shipmentId);

                $this->eventManager->dispatch('mcm_order_payout', ['shipment' => $shipment]);
            }
            catch (\Exception $e) {
                $failed++;
                $this->queueHelper->updateQueueMsgStatus($qItem['id'], 'error');
                continue;
            }

            $done++;
            $this->queueHelper->updateQueueMsgStatus($qItem['id'], 'done');
        }
        $output->writeln('Done. Got '. $i . ' shipments been processed.');
        $output->writeln('Invalid items: '. $invalid);
        $output->writeln('Succeeded: ' . $done);
        $output->writeln('Failed: ' . $failed);
    }
}