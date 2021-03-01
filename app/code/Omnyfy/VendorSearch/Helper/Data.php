<?php
/**
 * Created by PhpStorm.
 * User: Sanjaya-offline
 * Date: 14/01/2020
 * Time: 1:40 PM
 */

namespace Omnyfy\VendorSearch\Helper;


use Magento\Framework\App\Helper\Context;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const IS_ENABLED = 'vendor_search/options/is_active';

    const PAGE_TITLE = 'vendor_search/search_result/page_title';
    const IS_SEARCH_FORM = 'vendor_search/search_result/is_search_form';
    const IS_FILTERS = 'vendor_search/search_result/is_filters';
    const IS_DISTANCE = 'vendor_search/search_result/is_filter_distance';
    const LOCATION_URL = 'vendor_search/search_result/location_page';

    const LOCATION_LOCATION_URL = 'omnyfy_vendor/index/location';
    const BOOKING_LOCATION_URL = 'booking/practice/view';
    const VENDOR_URL = 'shop/brands/view';

    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    public function isEnabled(){
        return $this->scopeConfig->getValue(
            self::IS_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function isSearchForm(){
        return $this->scopeConfig->getValue(
            self::IS_SEARCH_FORM,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function isFilters(){
        return $this->scopeConfig->getValue(
            self::IS_FILTERS,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function isDistance(){
        return $this->scopeConfig->getValue(
            self::IS_DISTANCE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getPageTitle(){
        $title = $this->scopeConfig->getValue(
            self::PAGE_TITLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (empty($title))
            return "Search Results";

        return $title;
    }

    public function getLocationUrl(){
        $urlId = $this->scopeConfig->getValue(
            self::LOCATION_URL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($urlId == 1)
            return self::LOCATION_LOCATION_URL;

        if ($urlId == 2)
            return self::BOOKING_LOCATION_URL;
    }
}