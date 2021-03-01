<?php

namespace Omnyfy\Mcm\Observer;

use Magento\Framework\Event\ObserverInterface;
use Omnyfy\Mcm\Model\ResourceModel\FeesManagement as FeesManagementResource;
use Omnyfy\Mcm\Model\VendorPayout as VendorPayoutModel;
use Magento\Framework\Stdlib\DateTime\DateTime;

class OrderObserver implements ObserverInterface {
    /*
     * @var \Omnyfy\Mcm\Helper\Data
     */

    protected $_helper;
    protected $feesManagementResource;
    protected $vendorPayoutModel;
    protected $_date;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Sales\Model\Order\Item
     */
    protected $orderItem;

    /**
     * @var \Omnyfy\Mcm\Model\ResourceModel\VendorFeeReportAdmin
     */
    protected $vendorReportAdminResource;

    /**
     * @var \Omnyfy\Mcm\Model\ResourceModel\MarketplaceDetailedReport
     */
    protected $marketplaceDetailedReportResource;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Tax\Item
     */
    protected $taxItem;

    /**
     * @var \Omnyfy\Vendor\Helper\Data
     */
    protected $_vendorHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Omnyfy\Mcm\Model\VendorShipping
     */
    protected $vendorShipping;

    /**
     * @var \Omnyfy\Mcm\Model\CategoryCommissionReport
     */
    protected $categoryCommissionReport;

    /**
     * @var \Omnyfy\Mcm\Model\VendorOrder
     */
    protected $vendorOrder;

    protected $categoryCollectionFactory;

    protected $productRepository;

    /**
     * OrderObserver constructor.
     * @param FeesManagementResource $feesManagementResource
     * @param \Omnyfy\Mcm\Helper\Data $helper
     * @param VendorPayoutModel $vendorPayoutModel
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\Order\Item $orderItem
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Omnyfy\Mcm\Model\ResourceModel\VendorFeeReportAdmin $vendorReportAdminResource
     * @param \Omnyfy\Mcm\Model\ResourceModel\MarketplaceDetailedReport $marketplaceDetailedReportResource
     * @param \Omnyfy\Vendor\Helper\Data $_vendorHelper
     * @param \Magento\Sales\Model\ResourceModel\Order\Tax\Item $taxItem
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Omnyfy\Mcm\Model\VendorShipping $vendorShipping
     * @param \Omnyfy\Mcm\Model\CategoryCommissionReport $categoryCommissionReport
     * @param \Omnyfy\Mcm\Model\VendorOrder $vendorOrder
     * @param DateTime $date
     */
    public function __construct(
        FeesManagementResource $feesManagementResource,
        \Omnyfy\Mcm\Helper\Data $helper,
        VendorPayoutModel $vendorPayoutModel,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Order\Item $orderItem,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Omnyfy\Mcm\Model\ResourceModel\VendorFeeReportAdmin $vendorReportAdminResource,
        \Omnyfy\Mcm\Model\ResourceModel\MarketplaceDetailedReport $marketplaceDetailedReportResource,
        \Omnyfy\Vendor\Helper\Data $_vendorHelper,
        \Magento\Sales\Model\ResourceModel\Order\Tax\Item $taxItem,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Omnyfy\Mcm\Model\VendorShipping $vendorShipping,
        \Omnyfy\Mcm\Model\CategoryCommissionReport $categoryCommissionReport,
        \Omnyfy\Mcm\Model\VendorOrder $vendorOrder,
        DateTime $date,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
    ) {
        $this->feesManagementResource = $feesManagementResource;
        $this->_helper = $helper;
        $this->vendorPayoutModel = $vendorPayoutModel;
        $this->_date = $date;
        $this->orderRepository = $orderRepository;
        $this->orderItem = $orderItem;
        $this->quoteFactory = $quoteFactory;
        $this->vendorReportAdminResource = $vendorReportAdminResource;
        $this->marketplaceDetailedReportResource = $marketplaceDetailedReportResource;
        $this->taxItem = $taxItem;
        $this->_vendorHelper = $_vendorHelper;
        $this->scopeConfig = $scopeConfig;
        $this->vendorShipping = $vendorShipping;
        $this->categoryCommissionReport = $categoryCommissionReport;
        $this->vendorOrder = $vendorOrder;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->productRepository = $productRepository;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $order = $observer->getData('order');
        $orderId = $order->getId();
        if (empty($orderId)) {
            return;
        }

        $items = $order->getAllItems();
        $vendorIds = [];
        $locationIds = [];
        $categoryIds = [];
        $totalCategoryFee = 0;
        $totalSellerFee = 0;
        $totalDisbursementFee = 0;
        $totalDisbursementFeeTax = 0;
        $taxOnFees = 0;
        $totalTaxOnFees = 0;

        // report order data        
        $order = $this->orderRepository->get($orderId);
        $orderIncrementId = $order->getIncrementId();
        $orderIncrementId = str_pad($orderIncrementId, 9, '0', STR_PAD_LEFT);
        $shipping_and_hanldling_total = $order->getBaseShippingInclTax();
        $orderData = [
            'order_id' => $orderIncrementId,
            'shipping_and_hanldling_total' => $order->getBaseShippingInclTax(),
            'discount' => $order->getBaseDiscountAmount(),
            'order_total_value' => $order->getBaseGrandTotal()
        ];
        $this->vendorReportAdminResource->reportOrdersData($orderData);

        $marketplaceReportorderData = [
            'order_id' => $orderIncrementId,
            'shipping_and_hanldling_total' => $order->getBaseShippingInclTax(),
            'discount' => $order->getBaseDiscountAmount(),
            'order_total_value' => $order->getBaseGrandTotal(),
            'created_at' => $this->_date->gmtDate()
        ];

        $this->marketplaceDetailedReportResource->marketplacereportOrdersData($marketplaceReportorderData);


        foreach ($items as $item) {
            if ($item->getProductType() == 'configurable'
                || $item->getProductType() == 'mcp_product'
                || $item->getProductType() == 'mvcp') {
                continue;
            }
            /*
             * 2019-08-28 11:11 Jing Xiao
             * FOR issue with bundle product.
             * Assume bundle product only calculate fees on parent item.
             */
            $isChildOfBundle = false;
            if ($item->getParentItemId()) {
                $parentItem = $this->orderItem->load($item->getParentItemId());
                if ($parentItem->getProductType() != 'mvcp') {
                    $item = $this->orderItem->load($item->getParentItemId());
                }

                if ('bundle' == $item->getProductType()) {
                    $isChildOfBundle = true;
                }
            }
            if ($isChildOfBundle) {
                continue;
            }
            $itemId = $item->getItemId();
            $orderId = $item->getOrderId();
            $productId = $item->getProductId();
            $price = $item->getBasePrice();
            $discountAmount = $item->getBaseDiscountAmount();
            $vendorId = $item->getVendorId();
            $locationIds[] = $item->getLocationId();
            $itemQty = (int) $item->getQtyOrdered();
            $finalItemTotal = ($price * $itemQty) - $discountAmount;

            $categoryCollection = $this->categoryCollectionFactory->create();

            $product = $this->productRepository->getById($productId);
            $categoryIds = $product->getCategoryIds();
            $sellerFee = 0.00;
            $sellerFeeTax = 0.00;
            $categoryFeeTax = 0.00;
            $itemCatCommissionPercentage = 0.00;
            $category_commission_fee = 0.00;
            if (!empty($categoryIds)) {
                $categories = $categoryCollection
                        ->addAttributeToSelect('*')
                        ->addAttributeToFilter('entity_id', $categoryIds);

                $itemCatCommissionPercentage = 0;
                foreach ($categories as $category) {
                    $category_commission = $category->getCategoryCommissionPercentage();
                    $itemCatCommissionPercentage += $category_commission;
                    if (!empty($category_commission)) {
                        $collections = $this->categoryCommissionReport->getCollection()->addFieldToFilter('category_id', $category->getId());
                        $commissionData = $collections->getData();
                        if (!empty($commissionData)) {
                            foreach ($collections as $collection) {
                                $updateEarned = $collection['category_commission_earned'] + ($finalItemTotal * $category->getCategoryCommissionPercentage()) / 100;
                                $collection->setCategoryName($category->getName());
                                $collection->setCategoryCommissionPercentage($category->getCategoryCommissionPercentage());
                                $collection->setCategoryCommissionEarned($updateEarned);
                            }
                            $collections->save();
                        } else {
                            $data = [
                                'category_id' => $category->getId(),
                                'category_name' => $category->getName(),
                                'category_commission_percentage' => $category_commission,
                                'category_commission_earned' => ($finalItemTotal * $category->getCategoryCommissionPercentage()) / 100,
                                'created_at' => $this->_date->gmtDate()
                            ];
                            $this->categoryCommissionReport->setData($data);
                            $this->categoryCommissionReport->save();
                        }
                    }
                }
            } else {
                $category_commission = 0;
                $itemCatCommissionPercentage = 0;
            }

            if ($this->_helper->isEnable()) {
                $vendorTaxRate = $this->feesManagementResource->getVendorTaxRateByVendorId($vendorId);
            } else {
                $vendorTaxRate = 0;
            }

            if ($this->_helper->isEnable() && $this->_helper->allowCategoryCommisssion()) {
                $category_commission_fee = ($finalItemTotal * $itemCatCommissionPercentage) / 100;
                if (!empty($vendorTaxRate)) {
                    $categoryFeeTax = ($category_commission_fee * $vendorTaxRate) / 100;
                } else {
                    $categoryFeeTax = 0;
                }
            } else {
                $category_commission_fee = 0;
                $categoryFeeTax = 0;
            }

            if ($this->_helper->isEnable()) {
                $sellerFee = 0.00;
                if ($this->feesManagementResource->isVendorFeeActive($vendorId)) {
                    $sellerFeePercentage = $this->feesManagementResource->getSellerFeeByVendorId($vendorId);
                    $sellerMinFee = (double) $this->feesManagementResource->getSellerMinFeeByVendorId($vendorId);
                    $sellerMaxFee = (double) $this->feesManagementResource->getSellerMaxFeeByVendorId($vendorId);
                    $sellerFee = (double) ($finalItemTotal * $sellerFeePercentage) / 100;

                    if ($sellerMaxFee) {
                        if ($sellerFee > $sellerMaxFee) {
                            $sellerFee = $sellerMaxFee;
                        }
                    }
                    if ($sellerMinFee) {
                        if ($sellerFee < $sellerMinFee) {
                            $sellerFee = $sellerMinFee;
                        }
                    }
                } else {
                    if ($this->_helper->isVendorFeeEnable()) {
                        $sellerFeePercentage = (double) $this->_helper->getDefaultSellerFees();
                        $sellerFee = (double) ($finalItemTotal * $sellerFeePercentage) / 100;
                        $sellerMaxFee = (double) $this->_helper->getDefaultMaxSellerFees();
                        if ($sellerMaxFee) {
                            if ($sellerFee > $sellerMaxFee) {
                                $sellerFee = $sellerMaxFee;
                            }
                        }
                        $sellerMinFee = (double) $this->_helper->getDefaultMinSellerFees();
                        if ($sellerMinFee) {
                            if ($sellerFee < $sellerMinFee) {
                                $sellerFee = $sellerMinFee;
                            }
                        }
                    }
                }
                if (!empty($vendorTaxRate)) {
                    $sellerFeeTax = ($sellerFee * $vendorTaxRate) / 100;
                }
            } else {
                $sellerFee = 0;
                $sellerFeeTax = 0;
            }

            $row_total = $item->getBaseRowTotal();
            $tax_amount = $categoryFeeTax + $sellerFeeTax;
            $row_total_incl_tax = $item->getBaseRowTotalInclTax();

            $mcmorderItem = [];
            $mcmorderItem[] = [
                'order_id' => $orderId,
                'vendor_id' => $vendorId,
                'order_item_id' => $itemId,
                'seller_fee' => $sellerFee,
                'seller_fee_tax' => $sellerFeeTax,
                'category_commission_percentage' => $itemCatCommissionPercentage,
                'category_fee' => $category_commission_fee,
                'category_fee_tax' => $categoryFeeTax,
                'row_total' => $row_total,
                'tax_amount' => $tax_amount,
                'tax_percentage' => $vendorTaxRate,
                'row_total_incl_tax' => $row_total_incl_tax
            ];
            if ($this->_helper->isEnable()) {
                $this->feesManagementResource->saveMcmOrderItemRelation($mcmorderItem);
            }

            $vendorIds[] = $item->getData('vendor_id');
        }

//        $mcmVendorOrder = [];
        $vendorIds = array_unique($vendorIds);
        $locationIds = array_unique($locationIds);
        if (empty($vendorIds)) {
            //TODO: throw exception for no vendor ids
            return;
        }

        //save order vendor relationship
        foreach ($vendorIds as $orderVendorId) {
            if ($this->_helper->isEnable()) {
                $vendorTaxRate = $this->feesManagementResource->getVendorTaxRateByVendorId($orderVendorId);
                $vendorItemsTotals = $this->feesManagementResource->getVendorItemsTotals($orderVendorId, $orderId);
                $totalCategoryFee = $this->feesManagementResource->getTotalCategoryFee($orderVendorId, $orderId);
                $totalSellerFee = $this->feesManagementResource->getTotalSellerFee($orderVendorId, $orderId);
                $tax = $this->feesManagementResource->getTotalTaxOnFees($orderVendorId, $orderId);

                $disbursementFee = 0;
                $disbursementFeeTax = 0;
                if ($this->feesManagementResource->isVendorFeeActive($orderVendorId)) {
                    $disbursementFee = $this->feesManagementResource->getDisbursmentFeeByVendorId($orderVendorId);
                } else {
                    if ($this->_helper->isVendorFeeEnable()) {
                        $disbursementFee = $this->_helper->getDefaultDisbursementFees();
                    }
                }
                $disbursementFeeTax = ($disbursementFee * $vendorTaxRate) / 100;

                $totalTaxOnFees = round($tax + $disbursementFeeTax, 2);

                $vendorOrderTotals = $this->vendorPayoutModel->getResource()->getVendorOrderTotals($orderId, $orderVendorId);
                $payoutAmount = 0.00;
                $vendorTotal = 0.00;
                $vendorTotalInclTax = 0.00;
                $totalCategoryFeeTax = 0.00;
                $totalSellerFeeTax = 0.00;
                if (!empty($vendorOrderTotals)) {
                    $vendorTotal = $vendorOrderTotals['row_total'];
                    $vendorTotalInclTax = $vendorOrderTotals['row_total_incl_tax'];
                    $totalCategoryFeeTax = $vendorOrderTotals['category_fee_tax'];
                    $totalSellerFeeTax = $vendorOrderTotals['seller_fee_tax'];
                }

                if (!empty($vendorItemsTotals)) {
                    $grandTotal = $vendorItemsTotals['row_total'] + $vendorItemsTotals['tax_amount'] - $vendorItemsTotals['discount_amount'];
                    $payoutAmount = $grandTotal - ($totalCategoryFee + $totalSellerFee + $disbursementFee + $totalTaxOnFees);
                }

                $mcmVendorOrder = [
                    'order_id' => $orderId,
                    'order_increment_id' => $order->getIncrementId(),
                    'vendor_id' => $orderVendorId,
                    'total_category_fee' => $totalCategoryFee,
                    'total_category_fee_tax' => $totalCategoryFeeTax,
                    'total_seller_fee' => $totalSellerFee,
                    'total_seller_fee_tax' => $totalSellerFeeTax,
                    'disbursement_fee' => $disbursementFee,
                    'disbursement_fee_tax' => $disbursementFeeTax,
                    'total_tax_onfees' => $totalTaxOnFees,
                    'vendor_total' => $vendorTotal,
                    'vendor_total_incl_tax' => $vendorTotalInclTax,
                    'payout_amount' => $payoutAmount,
                    'subtotal' => $vendorItemsTotals['row_total'],
                    'base_subtotal' => $vendorItemsTotals['base_row_total'],
                    'subtotal_incl_tax' => $vendorItemsTotals['row_total_incl_tax'],
                    'base_subtotal_incl_tax' => $vendorItemsTotals['base_row_total_incl_tax'],
                    'tax_amount' => $vendorItemsTotals['tax_amount'],
                    'base_tax_amount' => $vendorItemsTotals['base_tax_amount'],
                    'discount_amount' => $vendorItemsTotals['discount_amount'],
                    'base_discount_amount' => $vendorItemsTotals['base_discount_amount'],
                    'grand_total' => $grandTotal,
                    'base_grand_total' => ($vendorItemsTotals['base_row_total'] + $vendorItemsTotals['base_tax_amount'] - ($vendorItemsTotals['base_discount_amount'])),
                    'created_at' => $this->_date->gmtDate(),
                    'updated_at' => $this->_date->gmtDate(),
                ];
                $this->feesManagementResource->saveMcmVendorOrderRelation($mcmVendorOrder);
            }
        }
        if ($this->_helper->isEnable()) {
            $orderTotal = number_format((float) $order->getGrandTotal(), 2, '.', '');
            $totalFeesTaxOnOrder = $this->feesManagementResource->getTaxOnFeesByOrderId($orderId);
            $disbursementFee = $this->feesManagementResource->getDisbursementFeeByOrderId($orderId);
            $disbursementFeeTax = $this->feesManagementResource->getDisbursementTaxByOrderId($orderId);
            $category_commission_fee = $this->feesManagementResource->getTotalCategoryFeeByOrderId($orderId);
            $categoryFeeTax = $this->feesManagementResource->getTotalCategoryTaxByOrderId($orderId);
            $sellerFee = $this->feesManagementResource->getTotalSellerFeeByOrderId($orderId);
            $sellerFeeTax = $this->feesManagementResource->getTotalSellerTaxByOrderId($orderId);

            $updateOrderData = [
                'disbursement_fee' => ($disbursementFee + $disbursementFeeTax),
                'total_fee' => ($category_commission_fee + $categoryFeeTax + $sellerFee + $sellerFeeTax + $disbursementFee + $disbursementFeeTax),
                'gross_earnings' => ($category_commission_fee + $sellerFee + $disbursementFee),
                'tax' => ($categoryFeeTax + $sellerFeeTax + $disbursementFeeTax),
                'net_earnings' => ($category_commission_fee + $categoryFeeTax + $sellerFee + $sellerFeeTax + $disbursementFee + $disbursementFeeTax)
            ];
            $this->vendorReportAdminResource->updateReportOrdersData($updateOrderData, $orderTotal);

            $updateMarketplaceReportData = [
                'disbursement_fee' => ($disbursementFee + $disbursementFeeTax),
                'transaction_fees' => $order->getMcmTransactionFeeInclTax(),
                'gross_earnings' => ($category_commission_fee + $categoryFeeTax + $sellerFee + $sellerFeeTax + $disbursementFee + $disbursementFeeTax + $order->getMcmTransactionFeeInclTax()),
            ];

            $this->marketplaceDetailedReportResource->updateMarketplacereportOrdersData($updateMarketplaceReportData, $orderTotal);
        }
        $included = $order->getShippingInclTax() - $order->getShippingAmount() > 0.0001 ? true : false;
        $baseToOrderRate = $order->getBaseToOrderRate();

        $shipping_amount = 0;
        $base_shipping_amount = 0;
        $shipping_tax = 0;
        $base_shipping_tax = 0;
        $shipping_incl_tax = 0;
        $base_shipping_incl_tax = 0;

        // load all shipping tax percentage
        $tax_items = $this->taxItem->getTaxItemsByOrderId($order->getId());
        $percentages = $this->_helper->getShippingTaxPercent($order->getId());

//        foreach ($tax_items as $tax_item) {
//            if ($tax_item['taxable_item_type'] == 'shipping') {
//                $percentages[] = $tax_item['tax_percent'];
//            }
//        }
        $quoteId = $order->getQuoteId();
        $quote = $this->quoteFactory->create();
        $quote->load($quoteId);

        $shippingAddress = $quote->getShippingAddress();
        $rates = $shippingAddress->getAllShippingRates();

        $shippingMethod = $this->_vendorHelper->shippingMethodStringToArray($order->getShippingMethod());
        $shippingMethod = empty($shippingMethod) ? [$locationIds[0] => $order->getShippingMethod()] : $shippingMethod;
        $data = [];
        foreach ($rates as $rate) {
            $vendorId = $rate->getVendorId();

            //Jing Xiao 2019-03-29 11:43
            //Only selected shipping rates should be calculated
            $locationId = $rate->getLocationId();
            if (!array_key_exists($locationId, $shippingMethod)) {
                continue;
            }
            if ($shippingMethod[$locationId] != $rate->getCode()) {
                continue;
            }
            //End of fix Jing Xiao 2019-03-29 11:43

            $shipping_tax = 0;
            foreach ($percentages as $taxCode => $percentage) {
                $shipping_tax += $rate->getPrice() * $percentage / (100 + $percentage);
            }
            //$base_shipping_tax = $shipping_tax / $baseToOrderRate;
            $base_shipping_tax = $shipping_tax;
            $shipping_incl_tax = $rate->getPrice() + $shipping_tax;
            //$base_shipping_incl_tax = ($rate->getPrice() / $baseToOrderRate) + $base_shipping_tax;
            $base_shipping_incl_tax = $rate->getPrice() + $base_shipping_tax;

            $shipping_type = $this->scopeConfig->getValue('carriers/' . $rate->getCarrier() . '/type');
            $vendorNoonOrder = $this->feesManagementResource->getVendorNosOnOrder($orderId);
            if ($shipping_type == 'I') {
                $totalQtyOrdered = $order->getTotalQtyOrdered();
                $discountoneachproduct = $order->getBaseShippingDiscountAmount() / $totalQtyOrdered;
                $qtyforvendor = $this->feesManagementResource->getQtyForVendor($orderId, $vendorId);
                $shippingVendordiscount = $discountoneachproduct * $qtyforvendor;
            } elseif ($shipping_type == 'O') {
                $shippingVendordiscount = $order->getBaseShippingDiscountAmount() / $vendorNoonOrder;
            } else {
                $shippingVendordiscount = 0;
            }
            //$base_shipping_amount = $rate->getPrice() / $baseToOrderRate;
            $base_shipping_amount = $rate->getPrice();
            if ($shippingVendordiscount > $base_shipping_amount) {
                $shippingVendordiscount = $base_shipping_amount;
            }
            if ($this->_helper->isEnable() && $this->feesManagementResource->isVendorFeeActive($vendorId)) {
                $data = [
                    'order_id' => $orderId,
                    'vendor_id' => $vendorId,
                    'shipping_amount' => $rate->getPrice(),
                    'base_shipping_amount' => $base_shipping_amount,
                    'shipping_incl_tax' => number_format((float) $shipping_incl_tax, 4, '.', ''),
                    'base_shipping_incl_tax' => number_format((float) $base_shipping_incl_tax, 4, '.', ''),
                    'shipping_tax' => number_format((float) $shipping_tax, 4, '.', ''),
                    'base_shipping_tax' => number_format((float) $base_shipping_tax, 4, '.', ''),
                    'shipping_discount_amount' => number_format((float) $shippingVendordiscount, 4, '.', '')
                ];
                $this->vendorShipping->setData($data);
                $this->vendorShipping->save();
            }
        }

        foreach ($vendorIds as $orderVendorId) {
            if ($this->_helper->isEnable() && $this->feesManagementResource->isVendorFeeActive($orderVendorId)) {
                $collection = $this->vendorShipping->getCollection()->addFieldToFilter('vendor_id', $orderVendorId)->addFieldToFilter('order_id', $orderId);
                $vendorShippingData = $collection->getData();

                $shipping_amount = 0;
                $base_shipping_amount = 0;
                $shipping_incl_tax = 0;
                $base_shipping_incl_tax = 0;
                $shipping_tax = 0;
                $base_shipping_tax = 0;
                $shipping_discount_amount = 0;

                foreach ($vendorShippingData as $vendorData) {
                    $shipping_amount += $vendorData['shipping_amount'];
                    $base_shipping_amount += $vendorData['base_shipping_amount'];
                    $shipping_incl_tax += $vendorData['shipping_incl_tax'];
                    $base_shipping_incl_tax += $vendorData['base_shipping_incl_tax'];
                    $shipping_tax += $vendorData['shipping_tax'];
                    $base_shipping_tax += $vendorData['base_shipping_tax'];
                    $shipping_discount_amount += $vendorData['shipping_discount_amount'];
                }
                $updateMcmVendorOrder = [
                    'vendor_id' => $orderVendorId,
                    'order_id' => $orderId,
                    'shipping_amount' => $shipping_amount,
                    'base_shipping_amount' => $base_shipping_amount,
                    'shipping_incl_tax' => $shipping_incl_tax,
                    'base_shipping_incl_tax' => $base_shipping_incl_tax,
                    'shipping_tax' => $shipping_tax,
                    'base_shipping_tax' => $base_shipping_tax,
                    'shipping_discount_amount' => $shipping_discount_amount
                ];
                $this->feesManagementResource->updateMcmVendorOrderRelation($updateMcmVendorOrder);

                $collection = $this->vendorOrder->getCollection()->addFieldToFilter('vendor_id', $orderVendorId)->addFieldToFilter('order_id', $orderId);
                $vendorOrders = $collection->getData();

                foreach ($vendorOrders as $vendorOrderData) {
                    $orderIncrementId = str_pad($orderIncrementId, 9, '0', STR_PAD_LEFT);
                    $orderData = [
                        'order_id' => $orderIncrementId,
                        'vendor_id' => $orderVendorId,
                        'shipping_and_hanldling_total' => $vendorOrderData['shipping_amount'],
                        'discount' => $vendorOrderData['base_discount_amount'] + $vendorOrderData['shipping_discount_amount'],
                        'order_total_value' => $vendorOrderData['base_grand_total'] + $vendorOrderData['base_shipping_amount'] + $vendorOrderData['base_shipping_tax'] - $vendorOrderData['shipping_discount_amount'],
                        'disbursement_fee' => $vendorOrderData['disbursement_fee'] + $vendorOrderData['disbursement_fee_tax'],
                        'total_fee' => $vendorOrderData['total_category_fee'] + $vendorOrderData['total_seller_fee'] + $vendorOrderData['disbursement_fee'] + $vendorOrderData['total_tax_onfees'],
                        'tax' => $vendorOrderData['base_tax_amount'] + $vendorOrderData['base_shipping_tax'] - $vendorOrderData['total_tax_onfees'],
                        'gross_earnings' => $vendorOrderData['base_grand_total'] + $vendorOrderData['base_shipping_amount'] + $vendorOrderData['base_shipping_tax'] - $vendorOrderData['shipping_discount_amount'] - ($vendorOrderData['total_category_fee'] + $vendorOrderData['total_seller_fee'] + $vendorOrderData['disbursement_fee'] + $vendorOrderData['total_tax_onfees']),
                        'net_earnings' => $vendorOrderData['base_grand_total'] + $vendorOrderData['base_shipping_amount'] - $vendorOrderData['base_tax_amount'] - $vendorOrderData['shipping_discount_amount'] - ($vendorOrderData['total_category_fee'] + $vendorOrderData['total_seller_fee'] + $vendorOrderData['disbursement_fee']),
                        'created_at' => $this->_date->gmtDate()
                    ];

                    $this->vendorReportAdminResource->adminReportOrdersData($orderData);
                }
            }
        }
        foreach ($items as $item) {

            $itemId = $item->getItemId();
            $orderId = $item->getOrderId();
            $productId = $item->getProductId();
            $price = $item->getPrice();
            $vendorId = $item->getVendorId();
            $order = $this->orderRepository->get($orderId);
            $orderIncrementId = $order->getIncrementId();
            $orderIncrementId = str_pad($orderIncrementId, 9, '0', STR_PAD_LEFT);
            $itemOrdered = $this->orderItem->load($itemId);
            if ($this->_helper->isEnable() && $this->feesManagementResource->isVendorFeeActive($vendorId)) {
                $data = [];
                $data[] = [
                    'order_id' => $orderIncrementId,
                    'item_id' => $itemId,
                    'vendor_id' => $vendorId,
                    'product_sku' => $itemOrdered->getSku(),
                    'product_name' => $itemOrdered->getName(),
                    'price_paid' => $itemOrdered->getPrice(),
                    'category_commission' => ($category_commission_fee + $categoryFeeTax),
                    'seller_fee' => ($sellerFee + $sellerFeeTax),
                    'created_at' => $this->_date->gmtDate()
                ];

                $this->vendorReportAdminResource->reportOrdersItem($data);

                $marketplaceReport = [];
                $marketplaceReport[] = [
                    'order_id' => $orderIncrementId,
                    'vendor_id' => $vendorId,
                    'vendor_name' => $this->feesManagementResource->getVendorNameByVendorId($vendorId),
                    'item_id' => $itemId,
                    'product_sku' => $itemOrdered->getSku(),
                    'product_name' => $itemOrdered->getName(),
                    'price_paid' => $itemOrdered->getPrice(),
                    'category_commission' => $category_commission_fee + $categoryFeeTax,
                    'seller_fee' => $sellerFee + $sellerFeeTax,
                    'created_at' => $this->_date->gmtDate()
                ];
                $this->marketplaceDetailedReportResource->marketplacereportOrdersItem($marketplaceReport);
            }
        }
    }

}
