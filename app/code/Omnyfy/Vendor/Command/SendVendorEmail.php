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

class SendVendorEmail extends Command
{
    const VENDOR_NOTIFY_TEMPLATE = 'vendor_order_notification_template';

    protected $appState;

    protected $queueHelper;

    protected $vendorHelper;

    protected $_transportBuilder;

    protected $_storeManager;

    protected $_vendorResource;

    protected $_templateCollectionFactory;

    public function __construct(
        \Magento\Framework\App\State $state,
        \Omnyfy\Core\Helper\Queue $queueHelper,
        \Omnyfy\Vendor\Helper\Data $vendorHelper,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Magento\Email\Model\ResourceModel\Template\CollectionFactory $templateCollectionFactory
    )
    {
        $this->appState = $state;
        $this->queueHelper = $queueHelper;
        $this->vendorHelper = $vendorHelper;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->_vendorResource = $vendorResource;
        $this->_templateCollectionFactory = $templateCollectionFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('omnyfy:vendor:notification_email');
        $this->setDescription('Process Vendor Notification Emails');
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
            $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
        }

        //Prepare template Id
        $templates = $this->_templateCollectionFactory->create();
        $templates->addFieldToFilter('orig_template_code', self::VENDOR_NOTIFY_TEMPLATE)
            ->setPageSize(1);

        $template = $templates->getFirstItem();
        $templateId = empty($template) ? self::VENDOR_NOTIFY_TEMPLATE : $template->getId();

        if(empty($template)){
            $output->writeln('Sending vendor email process ended, please create "vendor_order_notification_template" in magento sales order email templated ');
            return;
        }

        $output->writeln('Start to process - with email template id '.$templateId);

        $i = $done = $failed = $invalid = 0;

        while($qItem = $this->queueHelper->takeMsgFromQueue('vendor_notification_email')) {
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
            $orderNumber = isset($itemData['order_number']) ? $itemData['order_number'] : $this->_vendorResource->getOrderNumberByOrderId($orderId);
            $vendorIds = $itemData['vendor_ids'];

            $vendors = $this->vendorHelper->getVendorsByIds($vendorIds);

            foreach ($vendors as $vendor){
                $vendorEmail = $vendor->getData("email");
                $vendorName = $vendor->getData("name");

                $emailTemplateVariables = array (
                    'order_id' => $orderId,
                    'order_number' => $orderNumber
                );

                $transport = $this->_transportBuilder->setTemplateIdentifier($templateId)
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )
                    ->setTemplateVars($emailTemplateVariables)
                    ->setFrom('general')
                    ->addTo($vendorEmail, $vendorName)
                    ->getTransport();
                $transport->sendMessage();

                $output->writeln('Sending Enquiry For Vendor '.$vendorEmail.'||'.$orderId);
            }

            $this->queueHelper->updateQueueMsgStatus($qItem['id'], 'done');
        }
        $output->writeln('Done. Got '. $i . ' items in total.');
        $output->writeln('Invalid items: '. $invalid);
        $output->writeln('Succeeded: '. $done);
        $output->writeln('Failed: ' . $failed);
    }
}