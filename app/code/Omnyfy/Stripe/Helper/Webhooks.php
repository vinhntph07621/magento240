<?php

namespace Omnyfy\Stripe\Helper;

use StripeIntegration\Payments\Exception\WebhookException;

class Webhooks
{
    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    protected $response;

    /**
     * @var \StripeIntegration\Payments\Logger\WebhooksLogger
     */
    protected $webhooksLogger;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Framework\App\CacheInterface
     */
    protected $cache;

    /**
     * @var \Omnyfy\Stripe\Helper\GetConfigData
     */
    protected $configData;

    /**
     * Webhooks constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\App\Response\Http $response
     * @param \StripeIntegration\Payments\Logger\WebhooksLogger $webhooksLogger
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\CacheInterface $cache
     * @param \Omnyfy\Stripe\Helper\GetConfigData $configData
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\App\Response\Http $response,
        \StripeIntegration\Payments\Logger\WebhooksLogger $webhooksLogger,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\App\CacheInterface $cache,
        \Omnyfy\Stripe\Helper\GetConfigData $configData
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->webhooksLogger = $webhooksLogger;
        $this->logger = $logger;
        $this->cache = $cache;
        $this->eventManager = $eventManager;
        $this->configData = $configData;
    }

    /**
     * @param $event
     * @param $stdEvent
     * @throws WebhookException
     */
    public function dispatchEvent()
    {
        try {
            // Retrieve the request's body and parse it as JSON
            $body = $this->request->getContent();
            $this->verifyWebhookSignature($body);

            $event = json_decode($body, true);
            $stdEvent = json_decode($body);

            if (empty($event['type']))
                throw new WebhookException(__("Unknown event type"));

            $eventType = "omnyfy_stripe_payments_webhook_" . str_replace(".", "_", $event['type']);

            // Magento 2 event names do not allow numbers
            $eventType = str_replace("p24", "przelewy", $eventType);

            $this->log("Received $eventType");

            $this->cache($event);

            $this->eventManager->dispatch($eventType, array(
                'arrEvent' => $event,
                'stdEvent' => $stdEvent,
                'object' => $event['data']['object']
            ));

            $this->log("200 OK");
        }
        catch (WebhookException $e)
        {
            $this->error($e->getMessage(), $e->statusCode);
        }
        catch (\Exception $e)
        {
            $this->log($e->getMessage());
            $this->log($e->getTraceAsString());
            $this->error($e->getMessage());
        }
    }

    /**
     * @param $msg
     * @param null $status
     */
    protected function error($msg, $status = null)
    {
        if ($status && $status > 0)
            $responseStatus = $status;
        else
            $responseStatus = 202;

        $this->response
            ->setStatusCode($responseStatus)
            ->setContent($msg);

        $this->log("$responseStatus $msg");
    }

    /**
     * @param $msg
     */
    protected function log($msg)
    {
        $this->webhooksLogger->addInfo($msg);
    }

    /**
     * @param $payload
     * @throws WebhookException
     */
    protected function verifyWebhookSignature($payload)
    {
        $signingSecret = $this->configData->getConnectAccountWebhooksSigningSecret();
        if (empty($signingSecret))
            return;

        try
        {
            if (!isset($_SERVER['HTTP_STRIPE_SIGNATURE']))
                throw new WebhookException("Webhook signature could not be found in the request payload", 400);

            $event = \Stripe\Webhook::constructEvent($payload, $_SERVER['HTTP_STRIPE_SIGNATURE'], $signingSecret);
        }
        catch(\UnexpectedValueException $e)
        {
            throw new WebhookException("Invalid webhook payload", 400);
        }
        catch(\Stripe\Error\SignatureVerification $e)
        {
            throw new WebhookException("Invalid webhook signature", 400);
        }
    }

    /**
     * @param $event
     * @throws WebhookException
     */
    protected function cache($event)
    {
        // Don't cache or check requests in development
        if (!empty($this->request->getQuery()->dev))
            return;

        if (empty($event['id']))
            throw new WebhookException("No event ID specified");

        if ($this->cache->load($event['id']))
            throw new WebhookException("Event with ID {$event['id']} has already been processed.", 202);

        $this->cache->save("processed", $event['id'], array('stripe_payments_webhooks_events_processed'), 24 * 60 * 60);
    }
}
