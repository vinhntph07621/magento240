<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 20/2/18
 * Time: 4:59 PM
 */
namespace Omnyfy\Vendor\Plugin\Quote\Address;

use Magento\Framework\DataObject\Copy;
use Magento\Quote\Model\Quote\Address;
use Magento\Sales\Api\Data\OrderInterfaceFactory as OrderFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Event\ManagerInterface;

class ToOrder
{
    /**
     * @var Copy
     */
    protected $objectCopyService;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    protected $vendorHelper;

    public function __construct(
        OrderFactory $orderFactory,
        Copy $objectCopyService,
        ManagerInterface $eventManager,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Omnyfy\Vendor\Helper\Data $vendorHelper
    )
    {
        $this->orderFactory = $orderFactory;
        $this->objectCopyService = $objectCopyService;
        $this->eventManager = $eventManager;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->vendorHelper = $vendorHelper;
    }

    public function aroundConvert($subject, callable $process, \Magento\Quote\Model\Quote\Address $object, $data = [])
    {
        $shippingMethod = $object->getShippingMethod();

        if (!is_array($shippingMethod)) {
            return $process($object, $data);
        }

        $orderData = $this->objectCopyService->getDataFromFieldset(
            'sales_convert_quote_address',
            'to_order',
            $object
        );

        if (isset($orderData['shipping_method']) && is_array($orderData['shipping_method'])) {
            $orderData['shipping_method'] = $this->vendorHelper->shippingMethodArrayToString($orderData['shipping_method']);
        }
        /**
         * @var $order \Magento\Sales\Model\Order
         */
        $order = $this->orderFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $order,
            array_merge($orderData, $data),
            '\Magento\Sales\Api\Data\OrderInterface'
        );
        $order->setStoreId($object->getQuote()->getStoreId())
            ->setQuoteId($object->getQuote()->getId())
            ->setIncrementId($object->getQuote()->getReservedOrderId());
        $this->objectCopyService->copyFieldsetToTarget('sales_convert_quote', 'to_order', $object->getQuote(), $order);
        $this->eventManager->dispatch(
            'sales_convert_quote_to_order',
            ['order' => $order, 'quote' => $object->getQuote()]
        );
        return $order;
    }
}