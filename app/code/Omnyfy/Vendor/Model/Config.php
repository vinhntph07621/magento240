<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 21/8/18
 * Time: 2:44 PM
 */
namespace Omnyfy\Vendor\Model;

class Config
{
    const XML_PATH_QTY_CHECK_MODE = 'omnyfy_vendor/vendor/qty_check_mode';

    const XML_PATH_VENDOR_SHARE_PRODS = 'omnyfy_vendor/vendor/is_sku_sharing';

    const XML_PATH_ADMIN_ACROSS_VENDOR = 'omnyfy_vendor/vendor/is_admin_across';

    const XML_PATH_ADMIN_SEE_ALL_SKU = 'omnyfy_vendor/vendor/is_admin_see_all_sku';

    const XML_PATH_SHOW_VENDOR_BIND = 'omnyfy_vendor/vendor/show_vendor_bind';

    const XML_PATH_BIND_INCLUDE_WAREHOUSE = 'omnyfy_vendor/vendor/bind_include_warehouse';

    const XML_PATH_INVOICE_BY = 'omnyfy_vendor/vendor/invoice_by';

    const XML_PATH_MO_VENDOR_IDS = 'omnyfy_vendor/vendor/mo_vendor_ids';

    const XML_PATH_INCLUDE_MO_PRODUCTS = 'omnyfy_vendor/vendor/include_mo_products';

    const XML_PATH_READONLY_MO_PRODUCTS = 'omnyfy_vendor/vendor/readonly_mo_products';

    const XML_PATH_VENDOR_TYPE_IDS = 'omnyfy_vendor/vendor/vendor_type_ids';

    const XML_PATH_MO_ABN = 'omnyfy_vendor/vendor/mo_abn';

    const XML_PATH_MO_NAME = 'general/store_information/name';

    const XML_PATH_SHOW_LOC_PRIORITY = 'omnyfy_vendor/vendor/show_location_priority';

    const XML_PATH_QTY_ACTIVE_VENDOR_ONLY = 'omnyfy_vendor/vendor/qty_active_vendor_only';

    const XML_PATH_QTY_ACTIVE_LOCATION_ONLY = 'omnyfy_vendor/vendor/qty_active_location_only';

    const XML_PATH_ORDER_WEBHOOK_ENDPOINT = 'omnyfy_vendor/vendor/order_webhook_endpoint';

    const XML_PATH_ORDER_COMPLETE_WEBHOOK_ENDPOINT = 'omnyfy_vendor/vendor/order_complete_webhook_endpoint';

    const QTY_CHECK_MODE_WAREHOUSE_ONLY = 2;

    const QTY_CHECK_MODE_ALL = 1;

    const INVOICE_BY_MO = 1;

    const INVOICE_BY_VENDOR = 2;

    protected $_scopeConfig;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_scopeConfig = $scopeConfig;
    }

    public function getQtyCheckMode()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_QTY_CHECK_MODE);
    }

    public function isVendorShareProducts()
    {
        return $this->_scopeConfig->isSetFlag(self::XML_PATH_VENDOR_SHARE_PRODS);
    }

    public function isAdminAcrossVendor()
    {
        return $this->_scopeConfig->isSetFlag(self::XML_PATH_ADMIN_ACROSS_VENDOR);
    }

    public function isShowVendorBind()
    {
        return $this->_scopeConfig->isSetFlag(self::XML_PATH_SHOW_VENDOR_BIND);
    }

    public function getInvoiceBy()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_INVOICE_BY);
    }

    public function getMOVendorIds()
    {
        $str = $this->_scopeConfig->getValue(self::XML_PATH_MO_VENDOR_IDS);
        $result = explode(',', $str);
        return empty($result) ? [] : $result;
    }

    public function isIncludeMoProducts()
    {
        return $this->_scopeConfig->isSetFlag(self::XML_PATH_INCLUDE_MO_PRODUCTS);
    }

    public function isReadonlyMoProducts()
    {
        return $this->_scopeConfig->isSetFlag(self::XML_PATH_READONLY_MO_PRODUCTS);
    }

    public function getCanBindVendorTypeIds()
    {
        $str = $this->_scopeConfig->getValue(self::XML_PATH_VENDOR_TYPE_IDS);
        $result = explode(',', $str);
        return empty($result) ? [] : $result;
    }

    public function getMoAbn()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_MO_ABN);
    }

    public function getMoName()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_MO_NAME);
    }

    public function isAdminSeeAllSku()
    {
        return $this->_scopeConfig->isSetFlag(self::XML_PATH_ADMIN_SEE_ALL_SKU);
    }

    public function isBindIncludeWarehouse()
    {
        return $this->_scopeConfig->isSetFlag(self::XML_PATH_BIND_INCLUDE_WAREHOUSE);
    }

    public function isShowLocationPriority()
    {
        return $this->_scopeConfig->isSetFlag(self::XML_PATH_SHOW_LOC_PRIORITY);
    }

    public function isQtyActiveVendorOnly()
    {
        return $this->_scopeConfig->isSetFlag(self::XML_PATH_QTY_ACTIVE_VENDOR_ONLY);
    }

    public function isQtyActiveLocationOnly()
    {
        return $this->_scopeConfig->isSetFlag(self::XML_PATH_QTY_ACTIVE_LOCATION_ONLY);
    }

    public function getOrderWebhookEndpoint()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_ORDER_WEBHOOK_ENDPOINT);
    }

    public function getCompleteOrderWebhookEndpoint()
    {
        return $this->_scopeConfig->getValue(self::XML_PATH_ORDER_COMPLETE_WEBHOOK_ENDPOINT);
    }
}
