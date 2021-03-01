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

class ProcessSubmitQueue extends Command
{
    protected $appState;

    protected $queueHelper;

    protected $eventManager;

    protected $orderRepository;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Omnyfy\Core\Helper\Queue $queueHelper,
        \Magento\Framework\Event\Manager $eventManager,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    )
    {
        $this->appState = $state;
        $this->queueHelper = $queueHelper;
        $this->eventManager = $eventManager;
        $this->orderRepository = $orderRepository;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('omnyfy:mcm:process_submit_queue');
        $this->setDescription('Process submit Order Queue');
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

        while($qItem = $this->queueHelper->takeMsgFromQueue('mcm_after_place_order')) {
            try {
                $i++;
                if (!isset($qItem['id']) || empty($qItem['id'])) {
                    $output->writeln('Got an item without id at ' . $i);
                    $invalid++;
                    continue;
                }
                if (!isset($qItem['message']) || empty($qItem['message'])) {
                    $this->queueHelper->updateQueueMsgStatus($qItem['id'], 'blocking');
                    $invalid++;
                    continue;
                }
                $itemData = json_decode($qItem['message'], true);
                if (empty($itemData) || !isset($itemData['order_id'])) {
                    $this->queueHelper->updateQueueMsgStatus($qItem['id'], 'blocking');
                    $invalid++;
                    continue;
                }
                $orderId = $itemData['order_id'];

                //LOAD order by order id
                $order = $this->orderRepository->get($orderId);

                $this->eventManager->dispatch('mcm_after_place_order', ['order' => $order]);

                $this->queueHelper->updateQueueMsgStatus($qItem['id'], 'done');
            } catch (\Exception $exception) {
                $output->writeln($exception->getMessage());

                $this->queueHelper->updateQueueMsgStatus($qItem['id'], 'pending');
            }
        }
        $output->writeln('Done. Got '. $i . ' orders been processed.');
        $output->writeln('Invalid items: '. $invalid);
    }
}