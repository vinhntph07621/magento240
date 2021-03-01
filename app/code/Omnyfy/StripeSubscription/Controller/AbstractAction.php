<?php
/**
 * Project: Stripe Subscription
 * User: jing
 * Date: 2019-08-08
 * Time: 00:21
 */
namespace Omnyfy\StripeSubscription\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

abstract class AbstractAction extends \Magento\Framework\App\Action\Action
{
    protected $_queueTopic = 'stripe_web_hook';

    protected $_logger;

    protected $_qHelper;

    protected $_webHookContentFactory;

    protected $resultJsonFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Core\Helper\Queue $qHelper,
        \Omnyfy\StripeSubscription\Model\WebHookContentFactory $webHookContentFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_logger = $logger;
        $this->_qHelper = $qHelper;
        $this->_webHookContentFactory = $webHookContentFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

        try{
            $body = $this->getRequest()->getContent();

            $this->_logger->debug($body);

            $model = $this->_webHookContentFactory->create();
            $model->setId(null);
            $model->setContent($body);
            $model->save();

            $this->_qHelper->sendDataToQueue(
                $this->_queueTopic,
                [
                    'content_id' => $model->getId()
                ]
            );

            $result->setData(['success' => 'OK']);

        }
        catch (\Exception $e)
        {
            $result->setData(['error' => $e->getMessage()]);
        }

        return $result;
    }
}
 