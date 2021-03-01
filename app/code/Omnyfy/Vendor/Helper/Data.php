<?php
/**
 * Project: Omnyfy Multi Vendor.
 * User: jing
 * Date: 11/4/17
 * Time: 11:09 AM
 */
namespace Omnyfy\Vendor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\UserContextInterface;

class Data extends AbstractHelper
{
    protected $resource;

    protected $locationFactory;

    protected $vendorFactory;

    protected $locationIds;

    protected $orderFactory;

    protected $quoteFactory;

    protected $vendorResource;

    protected $invoiceFactory;

    protected $orderTaxManagement;

    protected $inventoryResource;

    protected $vendorConfig;

    protected $shippingHelper;

    public function __construct(
        Context $context,
        \Magento\Framework\App\ResourceConnection $resource,
        \Omnyfy\Vendor\Model\LocationFactory $locationFactory,
        \Omnyfy\Vendor\Model\VendorFactory $vendorFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Omnyfy\Vendor\Model\Resource\Vendor $vendorResource,
        \Magento\Sales\Model\Order\InvoiceFactory $invoiceFactory,
        \Magento\Tax\Api\OrderTaxManagementInterface $orderTaxManagement,
        \Omnyfy\Vendor\Model\Resource\Inventory $inventoryResource,
        \Omnyfy\Vendor\Model\Config $vendorConfig,
        \Omnyfy\Vendor\Helper\Shipping $shippingHelper
    )
    {
        $this->resource = $resource;

        $this->locationFactory = $locationFactory;

        $this->vendorFactory = $vendorFactory;

        $this->orderFactory = $orderFactory;

        $this->quoteFactory = $quoteFactory;

        $this->vendorResource = $vendorResource;

        $this->invoiceFactory = $invoiceFactory;

        $this->orderTaxManagement = $orderTaxManagement;

        $this->inventoryResource = $inventoryResource;

        $this->vendorConfig = $vendorConfig;

        $this->shippingHelper = $shippingHelper;

        parent::__construct($context);
    }

    public function loadLocationIds($websiteId)
    {
        if (isset($this->locationIds[$websiteId])
            && is_array($this->locationIds[$websiteId])
            && !empty($this->locationIds[$websiteId])
        ) {
            return $this->locationIds[$websiteId];
        }

        $collection = $this->locationFactory->create()->getCollection();
        $collection->filterWebsite($websiteId);
        $collection->getSelect()->order('priority', 'desc');

        $result = [];
        foreach ($collection as $location) {
            $result[$location->getId()] = [
                'vendor_id' => $location->getVendorId(),
                'priority' => $location->getPriority()
            ];
        }
        $this->locationIds[$websiteId] = $result;
        return $result;
    }

    public function groupInventoryByLocationId($productId, $websiteId, &$vendorId, $activeVendorOnly=false, $activeLocationOnly=false)
    {
        return $this->inventoryResource->loadInventoryGroupedByLocation($productId, $websiteId,$vendorId, $activeVendorOnly, $activeLocationOnly);
    }

    public function getRoleIdsByName($roleName, $resource = null)
    {
        if (empty($roleName)) {
            return [];
        }

        if (null == $resource) {
            $resource = $this->resource;
        }

        $table = 'authorization_role';

        if (method_exists($resource, 'getTable')) {
            $table = $resource->getTable($table);
        } elseif (method_exists($resource, 'getTableName')) {
            $table = $resource->getTableName($table);
        }

        $connection = $resource->getConnection();

        $select = $connection->select()->from(
            $table,
            ['role_id']
        )->where(
            "role_name = ?",
            $roleName
        )->where(
            "role_type = ?",
            RoleGroup::ROLE_TYPE
        )->where(
            "user_type = ?",
            UserContextInterface::USER_TYPE_ADMIN
        );

        $roleIds = $connection->fetchCol($select);
        return $roleIds;
    }

    public function getStoreIdsByWebsiteIds($websiteIds)
    {
        $table = $this->resource->getTableName('store');
        $conn = $this->resource->getConnection();

        $select = $conn->select()->from(
            $table,
            ['store_id']
        )->where(
            "website_id in (?)",
            $websiteIds
        );

        $storeIds = $conn->fetchCol($select);
        return $storeIds;
    }

    public function convertShippingMethods($method, &$compareLocation)
    {
        $compareLocation = true;
        if (is_array($method)) {
            return $method;
        }
        elseif (is_string($method)) {
            if ('{' == substr($method, 0, 1)) {
                return $this->shippingMethodStringToArray($method);
            }
            else {
                $compareLocation = false;
                return [$method];

            }
        }
        else{
            return [];
        }
    }

    public function shippingMethodArrayToString($shippingMethodArray)
    {
        if (is_array($shippingMethodArray) && !empty($shippingMethodArray)) {
            return json_encode($shippingMethodArray);
        }
        return '';
    }

    public function shippingMethodStringToArray($shippingMethodString)
    {
        if (is_string($shippingMethodString)
            && '{' == substr($shippingMethodString, 0, 1)
            && '}' == substr($shippingMethodString, -1)
        ) {
            return json_decode($shippingMethodString, true);
        }
        return null;
    }

    public function getCarrierCode($shippingMethodString)
    {
        $methods = $this->shippingMethodStringToArray($shippingMethodString);
        if (empty($methods)) {
            return '';
        }
        $codes = [];
        foreach($methods as $locationId => $code) {
            $codeArr = explode('_', $code);
            $codes[$locationId] = $codeArr[0];
        }
        return json_encode($codes);
    }

    public function getMethodCode($shippingMethodString)
    {
        $methods = $this->shippingMethodStringToArray($shippingMethodString);
        if (empty($methods)) {
            return '';
        }
        $codes = [];
        foreach($methods as $locationId => $code) {
            $codeArr = explode('_', $code);
            $codes[$locationId] = $codeArr[1];
        }
        return json_encode($codes);
    }

    public function parseCodeToShippingMethodString($carrierCode, $methodCode)
    {
        if ('{' != substr($carrierCode, 0, 1) && '{' != substr($methodCode, 0, 1)) {
            return $carrierCode . '_' . $methodCode;
        }

        $cCodes = json_decode($carrierCode, true);
        $mCodes = json_decode($methodCode, true);

        $result = [];
        foreach($cCodes as $locationId => $code) {
            $result[$locationId] = $code . '_' . $mCodes[$locationId];
        }

        return $this->shippingMethodArrayToString($result);
    }

    public function shippingMethodArrayCodeToRateId($shippingMethodArray, $rates)
    {
        $result = [];
        foreach($shippingMethodArray as $locationId => $code) {
            foreach($rates as $rate) {
                if ($locationId == $rate->getLocationId() && $code == $rate->getCode())
                {
                    $result[$locationId] = $rate->getId();
                    break;
                }
            }
        }
        return $result;
    }

    public function getLimitCarrier($shippingMethodString)
    {
        $result = [];
        $flag = true;
        $methods = $this->convertShippingMethods($shippingMethodString, $flag);
        foreach($methods as $locationId => $code) {
            list($carrierCode, $methodCode) = explode('_', $code);
            $result[] = $carrierCode;
        }
        return array_unique($result);
    }

    public function getLocationIds($quote)
    {
        $locationIds = [];
        foreach($quote->getAllItems() as $item) {
            $locationIds[]= $item->getLocationId();
        }
        return array_unique($locationIds);
    }

    public function getLocationsInfo($items)
    {
        error_log('get locations');
        $toVendorIds = [];
        foreach($items as $item) {
            $toVendorIds[$item->getLocationId()] = $item->getVendorId();
        }


        $vendorIds = array_unique(array_values($toVendorIds));
        $locationIds = array_unique(array_keys($toVendorIds));
        $vendors = $this->getVendorsByIds($vendorIds);

        $locations = $this->getLocationsByIds($locationIds);
        $result = [];
        foreach($locations as $location) {
            if ($location->getVendorId() != $toVendorIds[$location->getId()]) {
                $vendor = $vendors->getItemById($toVendorIds[$location->getId()]);
                $vendorName = empty($vendor) ? $this->getVendorNameById($toVendorIds[$location->getId()]) : $vendor->getName();
                $vendorName = empty($vendorName) ? '' : $vendorName;
                $location->setData('vendor_name', $vendorName);
            }
            $result[] = $location;
        }

        return $result;
    }

    public function getBookingLocationIds($items)
    {
        $bookingLocationIds = $noneBookingLocationIds = [];
        foreach($items as $item) {
            $toVendorIds[$item->getLocationId()] = $item->getVendorId();
            if (!empty($item->getBookingId())) {
                $bookingLocationIds[] = $item->getLocationId();
            }
            else{
                $noneBookingLocationIds[] = $item->getLocationId();
            }
        }
        $bookingLocationIds = array_unique($bookingLocationIds);
        $noneBookingLocationIds = array_unique($noneBookingLocationIds);
        return array_diff($bookingLocationIds, $noneBookingLocationIds);
    }

    public function getLocationsByIds($locationIds)
    {
        if (empty($locationIds)) {
            return false;
        }
        if (!is_array($locationIds)) {
            $locationIds = [intval($locationIds)];
        }
        $collection = $this->locationFactory->create()->getCollection();
        $collection->addFieldToSelect('*');
        $collection->addFieldToFilter('entity_id', ['in' => $locationIds]);
        $collection->joinVendorInfo();

        return $collection;
    }

    public function getVendorsByIds($vendorIds)
    {
        if (empty($vendorIds)) {
            return false;
        }
        if (!is_array($vendorIds)) {
            $vendorIds = [intval($vendorIds)];
        }
        $collection = $this->vendorFactory->create()->getCollection();
        $collection->addFieldToFilter('entity_id', $vendorIds);

        return $collection;
    }

    public function groupItemsByLocation($items)
    {
        $result = [];
        foreach($items as $item) {
            $locationId = intval($item->getLocationId());
            if (empty($locationId)) continue;
            if (!array_key_exists($locationId, $result)) {
                $result[$locationId] = [];
            }
            $result[$locationId][] = $item;
        }
        return $result;
    }

    public function calculateVendorOrderTotal($orderId)
    {
        $order = $this->orderFactory->create();
        $order->load($orderId);
        if (!$order->getId()) {
            return false;
        }

        $items = $order->getAllItems();
        $total = [];
        $locationIds = [];
        foreach($items as $item) {
            $locationIds[] = $item->getLocationId();
            $vendorId = $item->getVendorId();
            if (!isset($total[$vendorId])) {
                $total[$vendorId] = [
                    'subtotal'              => 0.0,
                    'base_subtotal'         => 0.0,
                    'subtotal_incl_tax'     => 0.0,
                    'base_subtotal_incl_tax'=> 0.0,
                    'tax_amount'            => 0.0,
                    'base_tax_amount'       => 0.0,
                    'shipping_amount'       => 0.0,
                    'base_shipping_amount'  => 0.0,
                    'shipping_incl_tax'     => 0.0,
                    'base_shipping_incl_tax'=> 0.0,
                    'discount_amount'       => 0.0,
                    'base_discount_amount'  => 0.0,
                    'shipping_tax'          => 0.0,
                    'base_shipping_tax'     => 0.0,
                    'grand_total'           => 0.0,
                    'base_grand_total'      => 0.0
                ];
            }
            $total[$vendorId]['subtotal'] += $item->getRowTotal();
            $total[$vendorId]['base_subtotal'] += $item->getBaseRowtotal();
            $total[$vendorId]['subtotal_incl_tax'] += $item->getRowTotalInclTax();
            $total[$vendorId]['base_subtotal_incl_tax'] += $item->getBaseRowTotalInclTax();
            $total[$vendorId]['tax_amount'] += $item->getTaxAmount();
            $total[$vendorId]['base_tax_amount'] += $item->getBaseTaxAmount();
            $total[$vendorId]['discount_amount'] += $item->getDiscountAmount();
            $total[$vendorId]['base_discount_amount'] += $item->getBaseDiscountAmount();
        }

        $shippingPickupLocation = $this->shippingHelper->getShippingConfiguration('overall_pickup_location');
        if ($this->shippingHelper->getCalculateShippingBy() == 'overall_cart') {
            $locationIds = [];
            array_push($locationIds, $shippingPickupLocation);
        } else {
            $locationIds = array_unique($locationIds);
        }


        $included = $order->getShippingInclTax() - $order->getShippingAmount() > 0.0001 ? false : true;
        $baseToOrderRate = $order->getBaseToOrderRate();

        if ($order->getShippingAmount() > 0) {
            // load all shipping tax percentage
            $percentages = $this->getShippingTaxPercent($orderId);

            $quoteId = $order->getQuoteId();
            $quote = $this->quoteFactory->create();
            $quote->load($quoteId);

            $shippingAddress = $quote->getShippingAddress();
            $rates = $shippingAddress->getAllShippingRates();

            $shippingMethod = $this->shippingMethodStringToArray($order->getShippingMethod());
            $shippingMethod = empty($shippingMethod) ? [$locationIds[0] => $order->getShippingMethod()] : $shippingMethod;

            foreach($shippingMethod as $locationId => $code) {
                if ($this->shippingHelper->getCalculateShippingBy() != 'overall_cart') {
                    foreach ($rates as $rate) {
                        if ($rate->getLocationId() == $locationId && $code == $rate->getCode()) {
                            $vendorId = $rate->getVendorId();
                            $total[$vendorId]['shipping_amount'] += $rate->getPrice();
                            $total[$vendorId]['base_shipping_amount'] += $rate->getPrice() / $baseToOrderRate;

                            foreach ($percentages as $taxCode => $percentage) {
                                $shippingTaxAmount = $this->getTaxAmount($rate->getPrice(), $percentage, $included);
                                $total[$vendorId]['shipping_tax'] += $shippingTaxAmount;
                                $total[$vendorId]['base_shipping_tax'] += $shippingTaxAmount / $baseToOrderRate;
                            }
                        }
                    }
                }
            }
        }

        if ($this->shippingHelper->getCalculateShippingBy() != 'overall_cart') {

            foreach ($total as $vendorId => $totalData) {
                if ($included) {
                    $total[$vendorId]['shipping_incl_tax'] = $total[$vendorId]['shipping_amount'];
                    $total[$vendorId]['base_shipping_incl_tax'] = $total[$vendorId]['base_shipping_amount'];
                } else {
                    $total[$vendorId]['shipping_incl_tax'] =
                        $total[$vendorId]['shipping_amount'] + $total[$vendorId]['shipping_tax'];
                    $total[$vendorId]['base_shipping_incl_tax'] =
                        $total[$vendorId]['base_shipping_amount'] + $total[$vendorId]['base_shipping_tax'];
                }
                $total[$vendorId]['grand_total'] = $total[$vendorId]['subtotal_incl_tax'] + $total[$vendorId]['shipping_incl_tax'];
                $total[$vendorId]['base_grand_total'] = $total[$vendorId]['base_subtotal_incl_tax'] + $total[$vendorId]['base_shipping_incl_tax'];
            }
        }

        $totalData = [];
        foreach($total as $vendorId => $totalArr) {
            $totalData[] = [
                'vendor_id'             => $vendorId,
                'order_id'              => $orderId,
                'subtotal'              => $totalArr['subtotal'],
                'base_subtotal'         => $totalArr['base_subtotal'],
                'subtotal_incl_tax'     => $totalArr['subtotal_incl_tax'],
                'base_subtotal_incl_tax'=> $totalArr['base_subtotal_incl_tax'],
                'tax_amount'            => $totalArr['tax_amount'],
                'base_tax_amount'       => $totalArr['base_tax_amount'],
                'discount_amount'       => $totalArr['discount_amount'],
                'base_discount_amount'  => $totalArr['base_discount_amount'],
                'shipping_amount'       => $totalArr['shipping_amount'],
                'base_shipping_amount'  => $totalArr['base_shipping_amount'],
                'shipping_incl_tax'     => $totalArr['shipping_incl_tax'],
                'base_shipping_incl_tax'=> $totalArr['base_shipping_incl_tax'],
                'shipping_tax'          => $totalArr['shipping_tax'],
                'base_shipping_tax'     => $totalArr['base_shipping_tax'],
                'grand_total'           => $totalArr['grand_total'],
                'base_grand_total'      => $totalArr['base_grand_total']
            ];
        }

        //save vendor order total into database
        $this->vendorResource->saveOrderTotal($totalData, [
            'subtotal',
            'base_subtotal',
            'subtotal_incl_tax',
            'base_subtotal_incl_tax',
            'tax_amount',
            'base_tax_amount',
            'discount_amount',
            'base_discount_amount',
            'shipping_amount',
            'base_shipping_amount',
            'shipping_incl_tax',
            'base_shipping_incl_tax',
            'shipping_tax',
            'base_shipping_tax',
            'grand_total',
            'base_grand_total'
        ]);

        return true;
    }

    public function calculateVendorInvoiceTotal($invoiceId)
    {
        $invoice = $this->invoiceFactory->create();
        $invoice->load($invoiceId);
        if (!$invoice->getId()) {
            return false;
        }

        $items = $invoice->getAllItems();
        $total = [];
        $locationIds = [];
        foreach($items as $item) {
            /** @var $item \Magento\Sales\Model\Order\Invoice\Item */
            $locationIds[] = $item->getLocationId();
            $vendorId = $item->getVendorId();
            if (!isset($total[$vendorId])) {
                $total[$vendorId] = [
                    'subtotal'              => 0.0,
                    'base_subtotal'         => 0.0,
                    'subtotal_incl_tax'     => 0.0,
                    'base_subtotal_incl_tax'=> 0.0,
                    'tax_amount'            => 0.0,
                    'base_tax_amount'       => 0.0,
                    'shipping_amount'       => 0.0,
                    'base_shipping_amount'  => 0.0,
                    'shipping_incl_tax'     => 0.0,
                    'base_shipping_incl_tax'=> 0.0,
                    'discount_amount'       => 0.0,
                    'base_discount_amount'  => 0.0,
                    'shipping_tax'          => 0.0,
                    'base_shipping_tax'     => 0.0,
                    'grand_total'           => 0.0,
                    'base_grand_total'      => 0.0
                ];
            }
            $total[$vendorId]['subtotal'] += $item->getRowTotal();
            $total[$vendorId]['base_subtotal'] += $item->getBaseRowtotal();
            $total[$vendorId]['subtotal_incl_tax'] += $item->getRowTotalInclTax();
            $total[$vendorId]['base_subtotal_incl_tax'] += $item->getBaseRowTotalInclTax();
            $total[$vendorId]['tax_amount'] += $item->getTaxAmount();
            $total[$vendorId]['base_tax_amount'] += $item->getBaseTaxAmount();
            $total[$vendorId]['discount_amount'] += $item->getDiscountAmount();
            $total[$vendorId]['base_discount_amount'] += $item->getBaseDiscountAmount();
        }

        $included = $invoice->getShippingInclTax() - $invoice->getShippingAmount() > 0.0001 ? false : true;
        $baseToOrderRate = $invoice->getBaseToOrderRate();

        if ($invoice->getShippingAmount() > 0) {

            $quoteId = $invoice->getOrder()->getQuoteId();
            $quote = $this->quoteFactory->create();
            $quote->load($quoteId);

            $percentages = $this->getShippingTaxPercent($invoice->getOrderId());

            $shippingAddress = $quote->getShippingAddress();
            $rates = $shippingAddress->getAllShippingRates();

            $orderShippingMethod = $invoice->getOrder()->getShippingMethod();
            $shippingMethod = $this->shippingMethodStringToArray($orderShippingMethod);
            $shippingMethod = empty($shippingMethod) ? [$locationIds[0] => $orderShippingMethod] : $shippingMethod;

            foreach($shippingMethod as $locationId => $code) {
                foreach($rates as $rate) {
                    if ($rate->getLocationId() == $locationId && $code == $rate->getCode()) {
                        $vendorId = $rate->getVendorId();
                        if (!array_key_exists($vendorId, $total)) {
                            //item for this rate may already been removed.
                            continue;
                        }
                        $total[$vendorId]['shipping_amount'] += $rate->getPrice();
                        $total[$vendorId]['base_shipping_amount'] += ($rate->getPrice() / $baseToOrderRate);

                        foreach($percentages as $taxCode => $percentage) {
                            $shippingTaxAmount = $this->getTaxAmount($rate->getPrice(), $percentage, $included);
                            $total[$vendorId]['shipping_tax'] += $shippingTaxAmount;
                            $total[$vendorId]['base_shipping_tax'] += ($shippingTaxAmount / $baseToOrderRate);
                        }
                    }
                }
            }
        }

        foreach($total as $vendorId => $totalData) {
            if ($included) {
                $total[$vendorId]['shipping_incl_tax'] = $total[$vendorId]['shipping_amount'];
                $total[$vendorId]['base_shipping_incl_tax'] = $total[$vendorId]['base_shipping_amount'];
            }
            else{
                $total[$vendorId]['shipping_incl_tax'] =
                    $total[$vendorId]['shipping_amount'] + $total[$vendorId]['shipping_tax'];
                $total[$vendorId]['base_shipping_incl_tax'] =
                    $total[$vendorId]['base_shipping_amount'] + $total[$vendorId]['base_shipping_tax'];
            }
            $total[$vendorId]['grand_total'] = $total[$vendorId]['subtotal_incl_tax'] + $total[$vendorId]['shipping_incl_tax'];
            $total[$vendorId]['base_grand_total'] = $total[$vendorId]['base_subtotal_incl_tax'] + $total[$vendorId]['base_shipping_incl_tax'];
        }

        $totalData = [];
        foreach($total as $vendorId => $totalArr) {
            $totalData[] = [
                'vendor_id'             => $vendorId,
                'invoice_id'            => $invoiceId,
                'subtotal'              => $totalArr['subtotal'],
                'base_subtotal'         => $totalArr['base_subtotal'],
                'subtotal_incl_tax'     => $totalArr['subtotal_incl_tax'],
                'base_subtotal_incl_tax'=> $totalArr['base_subtotal_incl_tax'],
                'tax_amount'            => $totalArr['tax_amount'],
                'base_tax_amount'       => $totalArr['base_tax_amount'],
                'discount_amount'       => $totalArr['discount_amount'],
                'base_discount_amount'  => $totalArr['base_discount_amount'],
                'shipping_amount'       => $totalArr['shipping_amount'],
                'base_shipping_amount'  => $totalArr['base_shipping_amount'],
                'shipping_incl_tax'     => $totalArr['shipping_incl_tax'],
                'base_shipping_incl_tax'=> $totalArr['base_shipping_incl_tax'],
                'shipping_tax'          => $totalArr['shipping_tax'],
                'base_shipping_tax'     => $totalArr['base_shipping_tax'],
                'grand_total'           => $totalArr['grand_total'],
                'base_grand_total'      => $totalArr['base_grand_total']
            ];
        }

        //save vendor order total into database
        $this->vendorResource->saveInvoiceTotal($totalData, [
            'subtotal',
            'base_subtotal',
            'subtotal_incl_tax',
            'base_subtotal_incl_tax',
            'tax_amount',
            'base_tax_amount',
            'discount_amount',
            'base_discount_amount',
            'shipping_amount',
            'base_shipping_amount',
            'shipping_incl_tax',
            'base_shipping_incl_tax',
            'shipping_tax',
            'base_shipping_tax',
            'grand_total',
            'base_grand_total'
        ]);

        return true;
    }

    protected function getTaxAmount($amount, $percent, $included)
    {
        if ($included) {
            return $amount * $percent / (100 + $percent);
        }
        else{
            return $amount * $percent * 0.01;
        }
    }

    protected function getShippingTaxPercent($orderId)
    {
        $orderTaxDetails = $this->orderTaxManagement->getOrderTaxDetails($orderId);
        $itemTaxDetails = $orderTaxDetails->getItems();
        $result = [];
        foreach ($itemTaxDetails as $itemTaxDetail) {
            //Aggregate taxable items associated with shipping
            if ($itemTaxDetail->getType() == \Magento\Quote\Model\Quote\Address::TYPE_SHIPPING) {
                $itemAppliedTaxes = $itemTaxDetail->getAppliedTaxes();
                foreach ($itemAppliedTaxes as $itemAppliedTax) {
                    if (0 == $itemAppliedTax->getAmount() && 0 == $itemAppliedTax->getBaseAmount()) {
                        continue;
                    }
                    $result[$itemAppliedTax->getCode()] = $itemAppliedTax->getPercent();
                }
            }
        }
        return $result;
    }

    public function parseShippingMethod($method, $locationIds)
    {
        if (empty($method)) {
            return [];
        }

        $methodArr = $this->shippingMethodStringToArray($method);

        if (!empty($methodArr)) {
            return $methodArr;
        }

        $result = [];
        foreach($locationIds as $locationId) {
            $result[$locationId] = $method;
        }
        return $result;
    }

    public function getAddressLocationIds($quoteAddress)
    {
        $locationIds = [];
        foreach($quoteAddress->getAllItems() as $item) {
            $locationIds[] = $item->getLocationId();
        }
        return array_unique($locationIds);
    }

    public function getAllStores()
    {
        $result = [];
        $locations = $this->locationFactory->create()->getCollection();
        if (!($this->vendorConfig->isBindIncludeWarehouse())) {
            $locations->addFieldToFilter('is_warehouse', ['neq' => 1]);
        }
        $locations->joinVendorInfo();
        foreach($locations as $location) {
            $vendorId = $location->getVendorId();
            if (!array_key_exists($vendorId, $result)) {
                $result[$vendorId] = $location;
            }
        }

        return $result;
    }

    public static function isValidAbn($abn) {
        $weights = array(10, 1, 3, 5, 7, 9, 11, 13, 15, 17, 19);
        // Strip non-numbers from the acn
        $abn = preg_replace('/[^0-9]/', '', $abn);
        // Check abn is 11 chars long
        if (strlen($abn) != 11) {
            return false;
        }
        // Subtract one from first digit
        $abn[0] = ((int) $abn[0] - 1);
        // Sum the products
        $sum = 0;
        foreach (str_split($abn) as $key => $digit) {
            $sum += ($digit * $weights[$key]);
        }
        if (($sum % 89) != 0) {
            return false;
        }
        return true;
    }

    public function getDistanceExpression($lat, $lng, $useHaversine = true)
    {
        $radLat = $lat * M_PI /180;
        $radLon = $lng * M_PI /180;

        if ($useHaversine) {
            return '('
                .'12742*ASIN('
                    .'SQRT( '
                        .'POWER( SIN( (' . $radLat . ' - rad_lat) * 0.5 ), 2)'
                        .'+'
                        . cos($radLat) . ' * cos_lat * POW( SIN( (' . $radLon . ' - rad_lon ) * 0.5), 2)'
                    .')'
                .')'
            .')'
            ;
        }

        return '('
            .'6371*ACOS(ROUND('
                . cos($radLat) . ' * cos_lat * COS( rad_lon - ' . $radLon . ')'
                .'+ ('
                . sin($radLat) . '* sin_lat), 8)'.
            ')'.
        ')'
        ;
    }

    public function isEnabledLocationFlat($storeId) {
        //TODO: load config by store id
        return true;
    }

    public function saveQuoteShipping($quoteId, $methods) {
        $this->vendorResource->saveQuoteShipping($quoteId, $methods);
    }

    public function getQuoteShipping($quoteId) {
        return $this->vendorResource->getQuoteShipping($quoteId);
    }

    public function getVendorNameById($vendorId) {
        $name = $this->vendorResource->getVendorNameById($vendorId);
        return empty($name) ? 'Not Set' : $name;
    }

    public function isMoProduct($productId) {
        $vendorId = $this->vendorResource->getVendorIdByProductId($productId);
        if (empty($vendorId)) {
            return true;
        }
        return in_array($vendorId, $this->vendorConfig->getMOVendorIds());
    }

    public function getCanBindVendorTypeIds() {
        return $this->vendorConfig->getCanBindVendorTypeIds();
    }

    public function getInvoiceBy() {
        return $this->vendorConfig->getInvoiceBy();
    }

    public function getMoAbn() {
        return $this->vendorConfig->getMoAbn();
    }

    public function getMoName() {
        return $this->vendorConfig->getMoName();
    }

    public function getUpdatedLabelProperties($defaultProperties) {
        if ($this->_request->getModuleName() === 'sales' && $this->_request->getControllerName() === 'order' && $this->_request->getActionName() === 'invoice'){
            // customer account dashboard
            return "colspan='5' class='mark'";
        }elseif ($this->_request->getModuleName() === 'sales' && $this->_request->getControllerName() === 'order_invoice' && $this->_request->getActionName() === 'email') {
            // invoice emails
            return "colspan='3' class='mark'";
        }
        return $defaultProperties;
    }

    public function getVendorAbnByItems($items) {
        $vendorIds = [];
        foreach($items as $item) {
            $vendorIds[] = $item->getVendorId();
        }
        $vendorIds = array_unique($vendorIds);
        $collection = $this->getVendorsByIds($vendorIds);
        $collection->addAttributeToSelect('abn');
        $collection->load();
        $result = [];
        foreach($vendorIds as $vendorId) {
            $vendor = $collection->getItemById($vendorId);
            $result[$vendorId] = empty($vendor) ? '' : $vendor->getData('abn');
        }
        return $result;
    }
}
