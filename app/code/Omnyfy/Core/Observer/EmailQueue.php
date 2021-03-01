<?php
/**
 * Project: Omnyfy Core.
 * User: jing
 * Date: 10/8/18
 * Time: 2:18 PM
 */
namespace Omnyfy\Core\Observer;

class EmailQueue implements \Magento\Framework\Event\ObserverInterface
{
    protected $_queueHelper;
    protected $_logger;

    public function __construct(
        \Omnyfy\Core\Helper\Queue $queueHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_queueHelper = $queueHelper;
        $this->_logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $templateName = $observer->getEvent()->getTemplateName();
        $to = $observer->getEvent()->getTo();
        $dataPoints = $observer->getEvent()->getDataPoints();

        $dataMapping = $observer->getEvent()->getDataMapping();
        $dataMapping = empty($dataMapping) ? $this->getDefaultMapping() : $dataMapping;

        $topicCode = $observer->getEvent()->getTopicCode();

        $this->_queueHelper->sendMsgToQueue($topicCode, json_encode([
            'template' => $templateName,
            'to' => $to,
            'data' => $dataPoints,
            'data_mapping' => $dataMapping
        ]));
    }
}