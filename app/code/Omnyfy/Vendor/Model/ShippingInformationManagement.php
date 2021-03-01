<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\Vendor\Model;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\NoSuchEntityException;
use Psr\Log\LoggerInterface as Logger;
use \Magento\Quote\Model\QuoteAddressValidator;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\AddressInterface;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Quote\Model\ShippingFactory;
use Magento\Framework\App\ObjectManager;


/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ShippingInformationManagement implements \Magento\Checkout\Api\ShippingInformationManagementInterface
{
    /**
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * @var PaymentDetailsFactory
     */
    protected $paymentDetailsFactory;

    /**
     * @var \Magento\Quote\Api\CartTotalRepositoryInterface
     */
    protected $cartTotalsRepository;

    /**
     * Quote repository.
     *
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Logger.
     *
     * @var Logger
     */
    protected $logger;

    /**
     * Validator.
     *
     * @var QuoteAddressValidator
     */
    protected $addressValidator;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Quote\Model\Quote\TotalsCollector
     */
    protected $totalsCollector;

    /**
     * @var \Omnyfy\Vendor\Helper\Data
     */
    protected $helper;

    /**
     * @var \Magento\Quote\Api\Data\CartExtensionFactory
     */
    private $cartExtensionFactory;

    /**
     * @var \Magento\Quote\Model\ShippingAssignmentFactory
     */
    protected $shippingAssignmentFactory;

    /**
     * @var \Magento\Quote\Model\ShippingFactory
     */
    private $shippingFactory;

    protected $shippingHelper;

    /**
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Model\QuoteAddressValidator $addressValidator
     * @param Logger $logger
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        QuoteAddressValidator $addressValidator,
        Logger $logger,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        \Omnyfy\Vendor\Helper\Data $helper,
        \Omnyfy\Vendor\Helper\Shipping $shippingHelper,
        CartExtensionFactory $cartExtensionFactory = null,
        ShippingAssignmentFactory $shippingAssignmentFactory = null,
        ShippingFactory $shippingFactory = null
    ) {
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->paymentDetailsFactory = $paymentDetailsFactory;
        $this->cartTotalsRepository = $cartTotalsRepository;
        $this->quoteRepository = $quoteRepository;
        $this->addressValidator = $addressValidator;
        $this->logger = $logger;
        $this->addressRepository = $addressRepository;
        $this->scopeConfig = $scopeConfig;
        $this->totalsCollector = $totalsCollector;
        $this->helper = $helper;
        $this->shippingHelper = $shippingHelper;
        if (!$cartExtensionFactory) {
            $cartExtensionFactory = ObjectManager::getInstance()->get(CartExtensionFactory::class);
        }
        $this->cartExtensionFactory = $cartExtensionFactory;
        if (!$shippingAssignmentFactory) {
            $shippingAssignmentFactory = ObjectManager::getInstance()->get(ShippingAssignmentFactory::class);
        }
        $this->shippingAssignmentFactory = $shippingAssignmentFactory;
        if (!$shippingFactory) {
            $shippingFactory = ObjectManager::getInstance()->get(ShippingFactory::class);
        }
        $this->shippingFactory = $shippingFactory;
    }



    public function saveAddressInformation(
        $cartId,
       \Magento\Checkout\Api\Data\ShippingInformationInterface$addressInformation)
    {
        $shippingConfiguration = $this->shippingHelper->getCalculateShippingBy();
        if ($shippingConfiguration == 'overall_cart') {
            $shippingPickupLocation = $this->shippingHelper->getShippingConfiguration('overall_pickup_location');
        }
        $address = $addressInformation->getShippingAddress();
        $billingAddress = $addressInformation->getBillingAddress();
        $carrierCode = $addressInformation->getShippingCarrierCode();
        $methodCode = $addressInformation->getShippingMethodCode();

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        if (!$address->getCustomerAddressId()) {
            $address->setCustomerAddressId(null);
        }
        else{
            $addressData = $this->addressRepository->getById($address->getCustomerAddressId());
            $address = $quote->getShippingAddress()->importCustomerAddressData($addressData);
        }

        if (!$address->getCountryId()) {
            throw new StateException(__('Shipping address is not set'));
        }

        $address->setSaveInAddressBook($address->getSaveInAddressBook() ? 1 : 0);
        $address->setSameAsBilling($address->getSameAsBilling() ? 1 : 0);
        $address->setCollectShippingRates(true);

        $locationIds = $this->helper->getLocationIds($quote);
        $noShippingLocationIds = $this->helper->getBookingLocationIds($quote->getAllItems());

        $shippingMethodArray = $this->parseShippingMethod($carrierCode, $methodCode);
        if (empty($shippingMethodArray) && !empty($locationIds)) {
            foreach($locationIds as $locationId) {
                if ($shippingConfiguration == 'overall_cart' && !empty($shippingPickupLocation)) {
                    $locationId = $shippingPickupLocation;
                }
                $shippingMethodArray[$locationId] = $carrierCode . '_' . $methodCode;
            }
        }

        $quote = $this->prepareShippingAssignment(
            $quote,
            $address,
            $this->helper->shippingMethodArrayToString($shippingMethodArray)
        );

        $this->validateQuote($quote);
        $quote->setIsMultiShipping(false);

        $address->setShippingMethod(
            $this->helper->shippingMethodArrayToString($shippingMethodArray)
        );

        if ($billingAddress) {
            $quote->setBillingAddress($billingAddress);
        }

        try {
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new InputException(__('Unable to save shipping information. Please check input data.'));
        }

        $shippingAddress = $quote->getShippingAddress();

        $allRates = $shippingAddress->getAllShippingRates();

        if ($shippingConfiguration == 'overall_cart' && !empty($shippingPickupLocation)) {
            $locationId = $shippingPickupLocation;
            if (array_key_exists($locationId, $shippingMethodArray)) {
                foreach($allRates as $rate) {
                    if ($rate->getLocationId() == $locationId
                        && $shippingMethodArray[$locationId] == $rate->getCode()
                    ) {
                        continue;
                    }
                }
            }
            else {
                //throw exception
                throw new NoSuchEntityException(
                    __('Please specify method for : %1', $locationId)
                );
            }
        }
        else {
            foreach ($locationIds as $locationId) {
                if (in_array($locationId, $noShippingLocationIds)) {
                    continue;
                }
                if (array_key_exists($locationId, $shippingMethodArray)) {
                    foreach ($allRates as $rate) {
                        if ($rate->getLocationId() == $locationId
                            && $shippingMethodArray[$locationId] == $rate->getCode()
                        ) {
                            continue 2;
                        }
                    }
                    //throw exception
                    throw new NoSuchEntityException(
                        __('Carrier with such method not found: %1, %2', $locationId, $shippingMethodArray[$locationId])
                    );
                } else {
                    //throw exception
                    throw new NoSuchEntityException(
                        __('Please specify method for : %1', $locationId)
                    );
                }
            }
        }

        /** @var \Magento\Checkout\Api\Data\PaymentDetailsInterface $paymentDetails */
        $paymentDetails = $this->paymentDetailsFactory->create();
        $paymentDetails->setPaymentMethods($this->paymentMethodManagement->getList($cartId));
        $paymentDetails->setTotals($this->cartTotalsRepository->get($cartId));
        return $paymentDetails;
    }

    /**
     * Validate quote
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @throws InputException
     * @throws NoSuchEntityException
     * @return void
     */
    protected function validateQuote(\Magento\Quote\Model\Quote $quote)
    {
        if (0 == $quote->getItemsCount()) {
            throw new InputException(__('Shipping method is not applicable for empty cart'));
        }
    }

    protected function parseShippingMethod($carrierCode, $methodCode)
    {
        $shippingMethod = [];
        if ('{' == substr($carrierCode, 0, 1) ) {
            $carrierCodes = json_decode($carrierCode, true);
            $methodCodes = json_decode($methodCode, true);
            foreach($carrierCodes as $id => $cCode) {
                $shippingMethod[$id] = $cCode . '_' . $methodCodes[$id];
            }
        }
        return $shippingMethod;
    }

    /**
     * @param CartInterface $quote
     * @param AddressInterface $address
     * @param string $method
     * @return CartInterface
     */
    private function prepareShippingAssignment(CartInterface $quote, AddressInterface $address, $method)
    {
        $cartExtension = $quote->getExtensionAttributes();
        if ($cartExtension === null) {
            $cartExtension = $this->cartExtensionFactory->create();
        }

        $shippingAssignments = $cartExtension->getShippingAssignments();
        if (empty($shippingAssignments)) {
            $shippingAssignment = $this->shippingAssignmentFactory->create();
        } else {
            $shippingAssignment = $shippingAssignments[0];
        }

        $shipping = $shippingAssignment->getShipping();
        if ($shipping === null) {
            $shipping = $this->shippingFactory->create();
        }

        $shipping->setAddress($address);
        $shipping->setMethod($method);
        $shippingAssignment->setShipping($shipping);
        $cartExtension->setShippingAssignments([$shippingAssignment]);
        return $quote->setExtensionAttributes($cartExtension);
    }
}
