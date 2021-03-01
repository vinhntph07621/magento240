<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 4/8/17
 * Time: 5:30 PM
 */
namespace Omnyfy\Vendor\Plugin;

use Magento\Checkout\Model\Session as CheckoutSession;
use Omnyfy\Vendor\Helper\Data as  VendorHelper;
use Omnyfy\Vendor\Helper\Shipping as ShippingHelper;
use Magento\Quote\Api\CartItemRepositoryInterface as QuoteItemRepository;
use Omnyfy\Vendor\Model\Source\CalculateShipping as CalculateShipping;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Module\Manager as ModuleManager;

class DefaultConfigProvider
{
    protected $checkoutSession;

    protected $vendorHelper;

    protected $shippingHelper;

    protected $quoteItemRepository;

    protected $eventManager;

    protected $moduleManager;

    public function __construct(
        CheckoutSession $checkoutSession,
        VendorHelper $vendorHelper,
        ShippingHelper $shippingHelper,
        QuoteItemRepository $quoteItemRepository,
        EventManager $eventManager,
        ModuleManager $moduleManager
    )
    {
        $this->checkoutSession = $checkoutSession;

        $this->vendorHelper = $vendorHelper;

        $this->shippingHelper = $shippingHelper;

        $this->quoteItemRepository = $quoteItemRepository;

        $this->eventManager = $eventManager;
        
        $this->moduleManager = $moduleManager;
    }

    public function afterGetConfig($subject, $result){
        //load location info by quote
        $quoteId = $this->checkoutSession->getQuote()->getId();
        if ($quoteId) {
            $quoteItems = $this->quoteItemRepository->getList($quoteId);

            // Get the shipping configuration (overall or per vendor)
            $shippingConfiguration = $this->shippingHelper->getCalculateShippingBy();
            $messageContentEnable = $this->shippingHelper->getCheckoutShippingMessageEnable();
            $messageContent = $this->shippingHelper->getShippingMessageContent();
            $shippingMethodEnabled = $this->shippingHelper->getShippingMethods();

            if ($this->shippingHelper->getFreeShippingMessageConfig()) {
                $result['freeShippingThreshold'] = $this->shippingHelper->getFreeShippingThreshold();
                $result['addToCartUnderMessage'] = $this->shippingHelper->getAddToCartUnderMessage();
                $result['addToCartReachedMessage'] = $this->shippingHelper->getAddToCartReachedMessage();
                $result['shoppingCartUnderMessage'] = $this->shippingHelper->getShoppingCartMessageUnder();
                $result['shoppingCartReachedMessage'] = $this->shippingHelper->getShoppingCartMessageReached();
                $result['shoppingCartMessageFreeShipping'] = $this->shippingHelper->getShoppingCartMessageFreeShipping();
            }

            if ($shippingConfiguration == 'overall_cart') {
                $shippingPickupLocation = $this->shippingHelper->getShippingConfiguration('overall_pickup_location');
            }

            if ($shippingConfiguration == 'overall_cart' && !empty($shippingPickupLocation)) {
                $result['shippingConfiguration'] = 'overall_cart';
                $result['shippingOverallId'] = $shippingPickupLocation;
                $result['messageContentShipping'] = $messageContent;
                $result['isMessageContentEnable'] = $messageContentEnable;
                $result['shippingMethodEnabled'] = $shippingMethodEnabled;

                $locationLookups = [$shippingPickupLocation];

                $locations = $this->vendorHelper->getLocationsByIds($locationLookups);

                if (!empty($locations)) {
                    $data = [];
                    foreach ($locations as $location) {
                        $data[$location->getId()] = $location->getData();
                    }

                    $result['locationData'] = $data;
                }
                //$locations = $this->vendorHelper->getLocationsInfo($quoteItems);
            } else {
                $result['shippingConfiguration'] = 'per_vendor';
                $result['shippingOverallId'] = '';
                $result['messageContentShipping'] = $messageContent;
                $result['isMessageContentEnable'] = $messageContentEnable;
                $result['shippingMethodEnabled'] = $shippingMethodEnabled;
                $locations = $this->vendorHelper->getLocationsInfo($quoteItems);
                if (!empty($locations)) {
                    $data = [];
                    foreach ($locations as $location) {
                        $data[$location->getId()] = $location->getData();
                    }

                    $result['locationData'] = $data;
                }
            }
        }

        return $result;
    }
}