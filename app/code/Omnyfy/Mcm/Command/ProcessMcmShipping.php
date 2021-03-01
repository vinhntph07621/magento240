<?php

namespace Omnyfy\Mcm\Command;

use Omnyfy\Core\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProcessMcmShipping
 * @package Omnyfy\Mcm\Command
 */
class ProcessMcmShipping extends Command
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \Omnyfy\Core\Helper\Queue
     */
    protected $queueHelper;

    /**
     * @var \Magento\Framework\Event\Manager
     */
    protected $eventManager;

    /**
     * @var \Magento\Sales\Api\ShipmentRepositoryInterface
     */
    protected $shipmentRepository;

    /**
     * ProcessMcmShipping constructor.
     * @param \Magento\Framework\App\State $state
     * @param \Omnyfy\Core\Helper\Queue $queueHelper
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \Magento\Sales\Api\ShipmentRepositoryInterface $shipmentRepository
     */
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

    /**
     *
     */
    protected function configure()
    {
        $this->setName('omnyfy:mcm:process_mcm_shipping');
        $this->setDescription('Process Mcm Shipping Calculation');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
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

        $output->writeln('Start to process shipments');

        $i = $done = $failed = $invalid = 0;

        while($qItem = $this->queueHelper->takeMsgFromQueue('mcm_order_shipment_data')) {
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

            if (empty($qItem['message'])) {
                $this->queueHelper->updateQueueMsgStatus($qItem['id'], 'blocking');
                $invalid++;
                continue;
            }

            try {
                $this->eventManager->dispatch('mcm_order_process_shipping', [
                    'shipment' => $qItem['message'],
                    'queue_id' => $qItem['id']
                ]);
            }
            catch (\Exception $e) {
                $failed++;
                $this->queueHelper->updateQueueMsgStatus($qItem['id'], 'error');
                continue;
            }

            $done++;
            $this->queueHelper->updateQueueMsgStatus($qItem['id'], 'done');
        }

        $output->writeln('Done. '. $i . ' shipment calculations have been processed.');
        $output->writeln('Invalid items: '. $invalid);
        $output->writeln('Succeeded: ' . $done);
        $output->writeln('Failed: ' . $failed);
    }
}