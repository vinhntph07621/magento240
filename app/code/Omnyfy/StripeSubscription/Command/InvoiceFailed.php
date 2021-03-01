<?php
/**
 * Project: Stripe Subscription
 * User: jing
 * Date: 2019-08-08
 * Time: 14:44
 */
namespace Omnyfy\StripeSubscription\Command;

use Omnyfy\Core\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InvoiceFailed extends Command
{
    protected $_appState;

    protected $_storeManager;

    protected $_qHelper;

    protected $_helper;

    protected $_eventManager;

    protected $_requiredFields = [
        'subscription',
        'customer',
        'id',
        'status',
        'account_name',
        'total',
        'created',
    ];

    public function __construct(
        \Magento\Framework\App\State $state,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\Core\Helper\Queue $queueHelper,
        \Omnyfy\StripeSubscription\Helper\Data $helper,
        \Magento\Framework\Event\Manager $eventManager,
        string $name = null)
    {
        $this->_appState = $state;
        $this->_storeManager = $storeManager;
        $this->_qHelper = $queueHelper;
        $this->_helper = $helper;
        $this->_eventManager = $eventManager;
        parent::__construct($name);
    }

    protected function configure()
    {
        $this->setName('omnyfy:stripe:invoice_failed');
        $this->setDescription('Process invoice payment failed event');

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
        while($qItem = $this->_qHelper->takeDataFromQueue('stripe_invoice_payment_failed', $itemId)) {
            $i++;
            $contentId = $qItem['content_id'];

            $content = $this->_helper->getContentById($contentId);
            if (empty($content)) {
                $this->_qHelper->updateQueueMsgStatus($itemId, 'blocking');
                $invalid++;
                continue;
            }

            $dataError = false;
            foreach($this->_requiredFields as $field) {
                if (!isset($content['data']['object'][$field]) || empty($content['data']['object'][$field])) {
                    $dataError = true;
                    break;
                }
            }

            if ($dataError) {
                $this->_qHelper->updateQueueMsgStatus($itemId, 'error');
                $failed++;
                continue;
            }

            try {
                $data = [
                    'sub_gateway_id' => $content['data']['object']['subscription'],
                    'customer_gateway_id' => $content['data']['object']['customer'],
                    'invoice_gateway_id' => $content['data']['object']['id'],
                    'billing_date' => date('Y-m-d H:i:s', $content['object']['created']),
                    'billing_account_name' => $content['data']['object']['account_name'],
                    'billing_amount' => $content['data']['object']['total'] * 0.01,
                    'status' => \Omnyfy\VendorSubscription\Model\Source\HistoryStatus::STATUS_FAILED
                ];

                $this->_eventManager->dispatch('omnyfy_subscription_invoice_failed', [
                    'data' => $data,
                ]);

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