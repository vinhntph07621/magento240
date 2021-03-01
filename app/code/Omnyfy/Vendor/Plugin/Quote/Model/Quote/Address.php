<?php
/**
 * Project: Omnyfy Multi Vendor.
 * User: jing
 * Date: 20/4/17
 * Time: 11:57 AM
 */

namespace Omnyfy\Vendor\Plugin\Quote\Model\Quote;

class Address
{
    protected $_addressRateFactory;

    protected $_rateCollector;

    protected $_rateRequestFactory;

    protected $helper;

    protected $_locationResource;

    protected $shippingHelper;

    public function __construct(
        \Magento\Quote\Model\Quote\Address\RateFactory $addressRateFactory,
        \Magento\Quote\Model\Quote\Address\RateCollectorInterfaceFactory $rateCollector,
        \Magento\Quote\Model\Quote\Address\RateRequestFactory $rateRequestFactory,
        \Omnyfy\Vendor\Helper\Data $helper,
        \Omnyfy\Vendor\Model\Resource\Location $_locationResource,
        \Omnyfy\Vendor\Helper\Shipping $shippingHelper
    )
    {
        $this->_addressRateFactory = $addressRateFactory;
        $this->_rateCollector = $rateCollector;
        $this->_rateRequestFactory = $rateRequestFactory;
        $this->helper = $helper;
        $this->_locationResource = $_locationResource;
        $this->shippingHelper = $shippingHelper;
    }

    public function aroundRequestShippingRates(
        \Magento\Quote\Model\Quote\Address $subject,
        callable $proceed,
        \Magento\Quote\Model\Quote\Item\AbstractItem $item = null
    )
    {
        //request shipping rates for single item
        if (!is_null($item)) {
            return $proceed($item);
        }

        $isPos = false;
        //if there's location id in ext_shipping_info, we know it's pos
        $extInfo = $subject->getQuote()->getExtShippingInfo();
        $extInfo = empty($extInfo) ? [] : json_decode($extInfo, true);
        if (!empty($extInfo) && isset($extInfo['location_id'])) {
            $isPos = true;
        }

        if ($subject->getQuote()->getIsMultiShipping()) {
            //return $proceed($item);
        }

        //group items by location id
        $grouped = [];

        $allItems = $subject->getAllItems();

        $isMvcpItem = $this->checkIfMvcpProductPresent($allItems);
        $mvcpItemLocationId = $this->getMvcpLastOptionLocation($allItems);

        $shippingConfiguration = $this->shippingHelper->getCalculateShippingBy();
        if ($shippingConfiguration == 'overall_cart') {
            $shippingPickupLocation = $this->shippingHelper->getShippingConfiguration('overall_pickup_location');
        }

        foreach($allItems as $item) {
            if ($isMvcpItem && $mvcpItemLocationId != '') {
                $locationId = $mvcpItemLocationId;
            } else {
                $locationId = $item->getLocationId();
            }

            if (empty($locationId) && $item instanceof \Magento\Quote\Model\Quote\Address\Item ) {
                $locationId = $item->getQuoteItem()->getLocationId();
            }

            if (empty($locationId)) {
                //TODO: throw exception
                return false;
            }

            // if set to overall cart, all items will be coming from the pickup address
            if ($shippingConfiguration == 'overall_cart' && !empty($shippingPickupLocation)) {
                $locationId = $shippingPickupLocation;
            }

            //booking product no shipping needed
            if (!empty($item->getBookingId())) {
                continue;
            }

            if (!isset($grouped[$locationId])) {
                $grouped[$locationId] = [
                    'items' => [],
                    'package_value' => 0,
                    'package_with_discount' => 0,
                    'package_weight' => 0,
                    'package_qty' => 0,
                    'package_physical_value' => 0
                ];
            }
            //Keep all items in array, but only calculate parent items for package info.
            $grouped[$locationId]['items'][] = $item;

            //Do not ignore virtual product
            if ( $item->getParentItem()) {
                continue;
            }

            $packageValue = $item->getBaseRowTotal();
            $shipValue = $item->getShipValue();
            $packageValue = empty($shipValue) ? $packageValue : $shipValue;
            $grouped[$locationId]['package_value'] += $packageValue;
            $grouped[$locationId]['package_with_discount'] += $packageValue - $item->getBaseDiscountAmount();
            $grouped[$locationId]['package_weight'] += $item->getRowWeight();
            $grouped[$locationId]['package_qty'] += $item->getQty();
            $grouped[$locationId]['package_physical_value'] += $item->getBaseRowTotal();
        }

        if (empty($grouped)) {
            //TODO: throw exception
            return false;
        }

        //Even there's only one group, we need to override the origin in request as location info in our database.
        $locationIds = array_keys($grouped);

        $locationId2VendorId = $this->_locationResource->getVendorIdsByLocationIds($locationIds);

        //load current shipping method settings
        $shippingMethod = $subject->getShippingMethod();

        $methods = $this->helper->shippingMethodStringToArray($shippingMethod);

        // if there's only one method
        if (empty($methods) && (1 == count($grouped))) {
            $locationId = $locationIds[0];
            $methods = [$locationId => $shippingMethod];
        }

        // load all locations by location ids
        $locations = $this->helper->getLocationsByIds($locationIds);

        $allFound = true;

        $shippingAmount = 0;
        foreach($grouped as $locationId => $data) {
            /** @var $request \Magento\Quote\Model\Quote\Address\RateRequest */
            $request = $this->_rateRequestFactory->create();
            $request->setAllItems($data['items']);
            $request->setDestCountryId($subject->getCountryId());
            $request->setDestRegionId($subject->getRegionId());
            $request->setDestRegionCode($subject->getRegionCode());
            $request->setDestStreet($subject->getStreetFull());
            $request->setDestCity($subject->getCity());
            $request->setDestPostcode($subject->getPostcode());
            $request->setDestRegionId($subject->getRegionId());
            $request->setDestRegionCode($subject->getRegionCode());
            $request->setDestStreet($subject->getStreetFull());
            $request->setDestCity($subject->getCity());
            $request->setDestPostcode($subject->getPostcode());
            $request->setPackageValue($data['package_value']);
            $request->setPackageValueWithDiscount($data['package_with_discount']);
            $request->setPackageWeight($data['package_weight']);
            $request->setPackageQty($data['package_qty']);

            if ($isPos) {
                $request->setIsPos('1');
            }

            /**
             * Need for shipping methods that use insurance based on price of physical products
             */
            $request->setPackagePhysicalValue($data['package_physical_value']);
            $request->setFreeMethodWeight($subject->getFreeMethodWeight());
            /**
             * Store and website identifiers need specify from quote
             */
            $request->setStoreId($subject->getQuote()->getStore()->getId());
            $request->setWebsiteId($subject->getQuote()->getStore()->getWebsiteId());
            $request->setFreeShipping($subject->getFreeShipping());

            /**
             * Currencies need to convert in free shipping
             */
            $request->setBaseCurrency($subject->getQuote()->getStore()->getBaseCurrency());
            $request->setPackageCurrency($subject->getQuote()->getStore()->getCurrentCurrency());
            $request->setLimitCarrier($subject->getLimitCarrier());
            $request->setBaseSubtotalInclTax($subject->getBaseSubtotalTotalInclTax());

            $request->setDestFirstname($subject->getFirstname());
            $request->setDestLastName($subject->getLastname());
            $request->setAddressId($subject->getId());

            //$request->setOrig(true) and set origin based on location id
            $location = $locations->getItemById($locationId);
            if (!empty($location)) {
                $request->setLocationId($locationId);

                $request
                    ->setOrigAddress($location->getAddress())
                    ->setOrigCountryId($location->getCountry())
                    ->setOrigRegionId($location->getRegionId())
                    ->setOrigState($location->getRegion())
                    ->setOrigCity($location->getSuburb())
                    ->setOrigPostcode($location->getPostcode())
                    ;
                $request->setOrig(true);
            }

            $result = $this->_rateCollector->create()->collectRates($request)->getResult();

            $found = false;
            if ($result) {
                $shippingRates = $result->getAllRates();

                foreach ($shippingRates as $shippingRate) {
                    $rate = $this->_addressRateFactory->create()->importShippingRate($shippingRate);
                    $rate->setData('location_id', $locationId);
                    if (array_key_exists($locationId, $locationId2VendorId)) {
                        $rate->setData('vendor_id', $locationId2VendorId[$locationId]);
                    }
                    if ($shippingRate->hasData('additional_data')) {
                        $rate->setData('additional_data', $shippingRate->getAdditionalData());
                    }

                    $subject->addShippingRate($rate);

                    if (isset($methods[$locationId]) && $methods[$locationId] == $rate->getCode()) {
                        $shippingAmount += $rate->getPrice();
                        $found = true;
                    }
                }
            }

            if (!$found) {
                $allFound = false;
            }
        }

        if ($allFound) {
            /**
             * possible bug: this should be setBaseShippingAmount(),
             * see \Magento\Quote\Model\Quote\Address\Total\Shipping::collect()
             * where this value is set again from the current specified rate price
             * (looks like a workaround for this bug)
             */
            $subject->setShippingAmount($shippingAmount);
        }
        return $allFound;
    }

    public function aroundGetShippingRateByCode($subject, callable $process, $shippingMethod)
    {
        if ('{' !== substr($shippingMethod, 0, 1)) {
            return $process($shippingMethod);
        }

        $methods = $this->helper->shippingMethodStringToArray($shippingMethod);
        foreach($subject->getShippingRatesCollection() as $rate) {
            foreach($methods as $locationId => $code) {
                if ($rate->getCode() == $code) {
                    return $rate;
                }
            }
        }

        return false;
    }

    public function checkIfMvcpProductPresent($allItems)
    {
        // Loop through items to check if there is an mvcp product
        foreach($allItems as $item) {
            if ($item->getProduct()->getTypeId() == 'mvcp') {
                return true;
            }
        }

        return false;
    }

    public function getMvcpLastOptionLocation($allItems)
    {
        $mvcpItemLocationId = '';
        $mvcpSortOrder = '';
        foreach($allItems as $item) {
            if ($mvcpItemLocationId == '' && $mvcpSortOrder == '') {
                $mvcpItemLocationId = $item->getLocationId();
                $mvcpSortOrder = $item->getSortOrder();
            } elseif ($item->getSortOrder() > $mvcpSortOrder) {
                $mvcpSortOrder = $item->getSortOrder();
                $mvcpItemLocationId = $item->getLocationId();
            }
        }

        return $mvcpItemLocationId;
    }
}
