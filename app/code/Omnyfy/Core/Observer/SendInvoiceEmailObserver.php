<?php
/**
 * Project: Send Invoice Email
 * Author: seth
 * Date: 31/3/20
 * Time: 11:30 am
 **/

namespace Omnyfy\Core\Observer;

use Magento\Sales\Model\Order\Email\Sender\InvoiceSender;
use Magento\Sales\Model\Order\Email\Sender\OrderSender;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order\Invoice;

class SendInvoiceEmailObserver implements ObserverInterface
{
    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var OrderSender
     */
    private $orderSender;

    /**
     * @var InvoiceSender
     */
    private $invoiceSender;

    protected $orderRepository;

    /**
     * @param \Psr\Log\LoggerInterface $logger
     * @param OrderSender $orderSender
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        OrderSender $orderSender,
        InvoiceSender $invoiceSender,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
    ) {
        $this->logger = $logger;
        $this->orderSender = $orderSender;
        $this->invoiceSender = $invoiceSender;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderIds = $observer->getEvent()->getOrderIds();

        try {
            foreach ($orderIds as $orderId) {
                $order = $this->orderRepository->get($orderId);
                foreach ($order->getInvoiceCollection()->getItems() as $invoice) {
                    if ($invoice->getState() == Invoice::STATE_PAID) {
                        $this->invoiceSender->send($invoice);
                    }
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e->getMessage());
        }

    }
}