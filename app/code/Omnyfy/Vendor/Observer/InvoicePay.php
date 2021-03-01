<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 21/6/17
 * Time: 3:45 PM
 */

namespace Omnyfy\Vendor\Observer;

use Magento\Framework\Event\ObserverInterface;
use Omnyfy\Vendor\Model\Resource\Vendor as VendorResource;

class InvoicePay implements ObserverInterface
{
    protected $vendorResource;

    protected $locationResource;

    protected $queueHelper;

    public function __construct(
        VendorResource $vendorResource,
        \Omnyfy\Vendor\Model\Resource\Location $locationResource,
        \Omnyfy\Core\Helper\Queue $queueHelper
    )
    {
        $this->vendorResource = $vendorResource;
        $this->locationResource = $locationResource;
        $this->queueHelper = $queueHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoice = $observer->getData('invoice');
        $invoiceId = $invoice->getId();
        if (empty($invoiceId)) {
            return;
        }

        //Only process for new invoice
        if ($invoice->getCreatedAt() !== $invoice->getUpdatedAt()) {
            return;
        }

        //get all vendor ids for this invoice and save into invoice-vendor relation
        $items = $invoice->getAllItems();
        $vendorIds = [];
        $locationIds = [];
        $qtyToDeduct = [];
        foreach($items as $item) {
            $vendorId = $item->getVendorId();
            $locationId = $item->getLocationId();
            if (!empty($vendorId)) {
                $vendorIds[] = $vendorId;
            }
            if (!empty($locationId)) {
                $locationIds[] = $locationId;
            }
            $qtyToDeduct[] = [
                'product_id' => $item->getProductId(),
                'location_id' => $item->getLocationId(),
                'qty' => $item->getQty()
            ];
        }
        $vendorIds = array_unique($vendorIds);
        $locationIds = array_unique($locationIds);

        if (empty($vendorIds)) {
            //TODO: throw exception or log errors
            return;
        }

        $customerId = $invoice->getOrder()->getCustomerId();

        $data = [];
        foreach($vendorIds as $vendorId) {
            $data[] = ['invoice_id' => $invoiceId, 'vendor_id' => $vendorId];
        }

        $this->vendorResource->saveInvoiceRelation($data);

        $this->vendorResource->deductQty($qtyToDeduct);

        //Save customer favorite vendor if location is not warehouse
        if (!empty($customerId) && count($locationIds) == 1) {
            $locationId = $locationIds[0];
            $warehouseIds = $this->locationResource->getWarehouseIds();

            if (!in_array($locationId, $warehouseIds)) {
                $vendorId = $vendorIds[0];
                $this->vendorResource->saveFavoriteVendorId($customerId, $vendorId);
            }
        }

        //add invoice id to queue for vendor total in invoice calculation
        $this->queueHelper->sendMsgToQueue('vendor_invoice_total', json_encode(['invoice_id' => $invoiceId]));
    }
}