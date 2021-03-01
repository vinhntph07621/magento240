<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-01
 * Time: 17:01
 */
namespace Omnyfy\VendorSubscription\Api\Data;

interface HistoryInterface
{
    const PLAN_ID = 'plan_id';

    const VENDOR_ID = 'vendor_id';

    const VENDOR_NAME = 'vendor_name';

    const BILLING_DATE = 'billing_date';

    const BILLING_ACCOUNT_NAME = 'billing_account_name';

    const PLAN_PRICE = 'plan_price';

    const BILLING_AMOUNT = 'billing_amount';

    const STATUS = 'status';

    const INVOICE_LINK = 'invoice_link';

    /**
     * @return int|null
     */
    public function getPlanId();

    /**
     * @param int $planId
     * @return $this
     */
    public function setPlanId($planId);

    /**
     * @return int|null
     */
    public function getVendorId();

    /**
     * @param int $vendorId
     * @return $this
     */
    public function setVendorId($vendorId);

    /**
     * @return string|null
     */
    public function getVendorName();

    /**
     * @param string $vendorName
     * @return $this
     */
    public function setVendorName($vendorName);
    /**
     * @return string|null
     */
    public function getBillingDate();

    /**
     * @param string $billingDate
     * @return $this
     */
    public function setBillingDate($billingDate);

    /**
     * @return string|null
     */
    public function getBillingAccountName();

    /**
     * @param string $billingAccountName
     * @return $this
     */
    public function setBillingAccountName($billingAccountName);

    /**
     * @return float|null
     */
    public function getPlanPrice();

    /**
     * @param float $planPrice
     * @return $this
     */
    public function setPlanPrice($planPrice);

    /**
     * @return float|null
     */
    public function getBillingAmount();

    /**
     * @param float $billingAmount
     * @return $this
     */
    public function setBillingAmount($billingAmount);

    /**
     * @return int|null
     */
    public function getStatus();

    /**
     * @param int $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return string|null
     */
    public function getInvoiceLink();

    /**
     * @param string $invoiceLink
     * @return mixed
     */
    public function setInvoiceLink($invoiceLink);
}
 