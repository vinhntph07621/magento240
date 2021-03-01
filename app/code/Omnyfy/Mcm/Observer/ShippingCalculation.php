<?php

namespace Omnyfy\Mcm\Observer;

use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Event\Observer;
use Omnyfy\Mcm\Model\ResourceModel\VendorOrder\CollectionFactory as VendorOrderCollectionFactory;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Omnyfy\Mcm\Model\ShippingCalculationFactory;
use Omnyfy\Mcm\Model\VendorOrderFactory;

/**
 * Class ShippingCalculation
 * @package Omnyfy\Mcm\Observer
 */
class ShippingCalculation implements ObserverInterface {

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Omnyfy\Mcm\Model\ResourceModel\VendorOrder
     */
    protected $vendorOrderResource;

    /**
     * @var ShippingCalculationFactory
     */
    protected $_shippingCalculationFactory;

    /**
     * @var \Omnyfy\Core\Helper\Queue
     */
    protected $queueHelper;

    protected $vendorOrderFactory;

    protected $payoutHelper;

    /**
     * ShippingCalculation constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Omnyfy\Mcm\Model\ResourceModel\VendorOrder $vendorOrderResource
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\App\RequestInterface $request,
        \Omnyfy\Mcm\Model\ShippingCalculationFactory  $shippingCalculationFactory,
        \Omnyfy\Core\Helper\Queue $queueHelper,
        \Omnyfy\Mcm\Model\VendorOrderFactory $vendorOrderFactory,
        \Omnyfy\Mcm\Helper\Payout $payoutHelper
    ) {
        $this->objectManager = $objectManager;
        $this->request = $request;
        $this->_shippingCalculationFactory = $shippingCalculationFactory;
        $this->queueHelper = $queueHelper;
        $this->vendorOrderFactory = $vendorOrderFactory;
        $this->payoutHelper = $payoutHelper;
    }

    /**
     * @param $observer
     */
    public function execute(Observer $observer)
    {
        $shipmentData = json_decode($observer->getEvent()->getShipment(), true);

        $queueId = $observer->getEvent()->getQueueId();

        $requiredFields = ['order_id', 'vendor_id', 'location_id', 'ship_by_type', 'type'];

        $hasError = false;
        foreach($requiredFields as $requiredField) {
            if (!isset($shipmentData[$requiredField])) {
                $this->queueHelper->updateQueueMsgStatus($queueId, 'error');
                $hasError = true;
            }
        }

        $existingShipment = $this->_shippingCalculationFactory
            ->create()
            ->getCollection()
            ->addFieldToFilter('order_id', $shipmentData['order_id'])
            ->addFieldToFilter('vendor_id', $shipmentData['vendor_id'])
            ->addFieldToFilter('location_id', $shipmentData['location_id'])
            ->getFirstItem();

        if ($existingShipment->getData() && $shipmentData['type'] == 'existing') {
            $shipmentMcm = $this->_shippingCalculationFactory->create()->load($existingShipment->getId());
            $shipmentMcmData = [
                'order_id' => $shipmentData['order_id'],
                'vendor_id' => $shipmentData['vendor_id'],
                'location_id' => $shipmentData['location_id'],
                'status' => $shipmentData['status'],
                'ship_by_type' => $shipmentData['ship_by_type']
            ];

            if (empty($shipmentData['total_charge']) || !isset($shipmentData['total_charge'])) {
                $shipmentMcmData['total_charge'] = 0;
            } else {
                $shipmentMcmData['total_charge'] = $shipmentData['total_charge'];
            }

            if (empty($shipmentData['total_charge']) || !isset($shipmentData['total_charge'])) {
                $shipmentMcmData['total_charge'] = 0;
            } else {
                $shipmentMcmData['total_charge'] = $shipmentData['total_charge'];
            }

            if (empty($shipmentData['customer_paid']) || !isset($shipmentData['customer_paid'])) {
                $shipmentMcmData['customer_paid'] = 0;
            } else {
                $shipmentMcmData['customer_paid'] = $shipmentData['customer_paid'];
            }

            $shipmentMcm->addData($shipmentMcmData);

            try {
                $saveData = $shipmentMcm->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e0) {
                $this->queueHelper->updateQueueMsgStatus($queueId, 'error');
                $this->objectManager->get('Psr\Log\LoggerInterface')->critical($e0->getMessage());
            } catch (\Exception $e) {
                $this->queueHelper->updateQueueMsgStatus($queueId, 'error');
                $this->objectManager->get('Psr\Log\LoggerInterface')->critical($e->getMessage());
            }
        }
        elseif (!$hasError && !$existingShipment->getData() && $shipmentData['type'] == 'new') {
            $shipmentMcm = $this->_shippingCalculationFactory->create();
            $shipmentMcmData = [
                'order_id' => $shipmentData['order_id'],
                'vendor_id' => $shipmentData['vendor_id'],
                'location_id' => $shipmentData['location_id'],
                'status' => $shipmentData['status'],
                'ship_by_type' => $shipmentData['ship_by_type']
            ];

            if (empty($shipmentData['total_charge']) || !isset($shipmentData['total_charge'])) {
                $shipmentMcmData['total_charge'] = 0;
            } else {
                $shipmentMcmData['total_charge'] = $shipmentData['total_charge'];
            }

            if (empty($shipmentData['total_charge']) || !isset($shipmentData['total_charge'])) {
                $shipmentMcmData['total_charge'] = 0;
            } else {
                $shipmentMcmData['total_charge'] = $shipmentData['total_charge'];
            }

            if (empty($shipmentData['customer_paid']) || !isset($shipmentData['customer_paid'])) {
                $shipmentMcmData['customer_paid'] = 0;
            } else {
                $shipmentMcmData['customer_paid'] = $shipmentData['customer_paid'];
            }

            $shipmentMcm->addData($shipmentMcmData);

            try {
                $saveData = $shipmentMcm->save();
            } catch (\Magento\Framework\Exception\LocalizedException $e0) {
                $this->queueHelper->updateQueueMsgStatus($queueId, 'error');
                $this->objectManager->get('Psr\Log\LoggerInterface')->critical($e0->getMessage());
            } catch (\Exception $e) {
                $this->queueHelper->updateQueueMsgStatus($queueId, 'error');
                $this->objectManager->get('Psr\Log\LoggerInterface')->critical($e->getMessage());
            }
        }

        // Update the payout amount of the mcm order
        $vendorOrderMcm = $this->vendorOrderFactory
            ->create()
            ->getCollection()
            ->addFieldToFilter('order_id', $shipmentData['order_id'])
            ->addFieldToFilter('vendor_id', $shipmentData['vendor_id'])
            ->getFirstItem();

        if ($vendorOrderMcm->getData()) {
            // If there is a record in omnyfy_mcm_shipping_calculation use that for order
            $doesShippingMcmCalculationExist = $this->payoutHelper->doesShippingCalculationExist($vendorOrderMcm);
            if ($doesShippingMcmCalculationExist) {
                $orderTotalIncludingShippingPayout = $this->payoutHelper->getOrderPayoutShippingAmount($vendorOrderMcm);
                $vendorOrderTotalIncTax = ($vendorOrderMcm->getBaseGrandTotal() + ($orderTotalIncludingShippingPayout - $vendorOrderMcm->getShippingDiscountAmount()));
                $vendorOrderMcm->setPayoutShipping($orderTotalIncludingShippingPayout - $vendorOrderMcm->getShippingDiscountAmount());
            } else {
                // fallback for existing
                $vendorOrderTotalIncTax = ($vendorOrderMcm->getBaseGrandTotal() + ($vendorOrderMcm->getBaseShippingAmount() + $vendorOrderMcm->getBaseShippingTax() - $vendorOrderMcm->getShippingDiscountAmount()));
            }

            $vendorOrderTotalFees = $vendorOrderMcm->getTotalCategoryFee() + $vendorOrderMcm->getTotalSellerFee() + $vendorOrderMcm->getDisbursementFee();
            $vendorOrderTotalFeeTax = $vendorOrderMcm->getTotalCategoryFeeTax() + $vendorOrderMcm->getTotalSellerFeeTax() + $vendorOrderMcm->getDisbursementFeeTax();
            $vendorOrderFeeTotalIncTax = $vendorOrderTotalFees + $vendorOrderTotalFeeTax;

            $vendorOrderMcm->setPayoutAmount($vendorOrderTotalIncTax - $vendorOrderFeeTotalIncTax);
            $vendorOrderMcm->setPayoutCalculated(1);

            try {
                $vendorOrderMcm->save();
            } catch (\Exception $e) {
                $this->objectManager->get('Psr\Log\LoggerInterface')->critical('Error updating payout amount order_id: ' . $shipmentData['order_id']
                    . 'vendor_id' . $shipmentData['vendor_id']
                    . $e->getMessage()
                );
            }
        }
    }
}
