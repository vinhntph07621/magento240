<?php
namespace Omnyfy\Vendor\Observer;

use Magento\Framework\Event\ObserverInterface;
use \Magento\Catalog\Model\ProductFactory;
use \Magento\Framework\App\RequestInterface;
use Magento\Catalog\Model\ProductRepository;
use \Magento\Checkout\Model\Cart;
use \Magento\Framework\Message\ManagerInterface ;

use \Magento\Checkout\Model\Session as CheckoutSession;

use Omnyfy\Vendor\Helper\Product as VendorProduct;


class UpdateMessage implements ObserverInterface
{
    /** @var CheckoutSession */
    protected $checkoutSession;
    /**
     * @var ProductFactory
     */
    protected $_productloader;
    /**
     * @var ProductRepository
     */
    protected $_productRepository;
    /**
     * @var RequestInterface
     */
    protected $_request;

    /**
     * @var Cart
     */
    protected $_cart;
    /**
     * @var ManagerInterface
     */
    protected $_messageManager;

    /** @var \Magento\Framework\UrlInterface */
    protected $url;

    /**
     * @param CheckoutSession $checkoutSession
     * @param ProductFactory $productLoader
     * @param RequestInterface $request
     * @param ProductRepository $productRepository
     * @param Cart $cart
     * @param ManagerInterface $messageManager
     * @param VendorProduct $vendorProduct
     * * @param VendorProduct $vendorData
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $managerInterface,
        \Magento\Framework\UrlInterface $url,
        CheckoutSession $checkoutSession,
        ProductFactory $productLoader,
        RequestInterface $request,
        ProductRepository $productRepository,
        cart $cart,
        ManagerInterface $messageManager,
        VendorProduct $vendorProduct,
        \Omnyfy\Vendor\Helper\Shipping $shippingHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->_productloader = $productLoader;
        $this->_request = $request;
        $this->_productRepository = $productRepository;
        $this->_cart = $cart;
        $this->_messageManager = $messageManager;
        $this->messageManager = $managerInterface;
        $this->url = $url;
        $this->vendorProduct = $vendorProduct;
        $this->shippingHelper = $shippingHelper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        if ($this->shippingHelper->getFreeShippingThreshold()) {
            $productId= $this->_request->getParam('product');
            $vendorIds = $this->vendorProduct->getVendorId($productId);
            $vendor = $this->vendorProduct->getVendor($vendorIds['vendor_ids'][0]);

            if (!empty($vendor->getVfreeShippingThreshold())) {
                $threshold = $vendor->getVfreeShippingThreshold();
            } else {
                $threshold = $this->shippingHelper->getFreeShippingThreshold();
            }

            $addToCartUnderMessage = str_replace('[vendor name]', $vendor->getName(), $this->shippingHelper->getAddToCartUnderMessage());
            $addToCartReachedMessage = str_replace('[vendor name]', $vendor->getName(), $this->shippingHelper->getAddToCartReachedMessage());

            $quote = $this->checkoutSession->getQuote();

            $items = $quote->getAllItems();
            $vendorTotal = 0;
            foreach ($items as $item) {
                if ($item->getVendorId() == $vendor->getId()) {
                    $vendorTotal = (int)$vendorTotal + (int)$item->getBaseRowTotal();
                }
            }

            if ($vendorTotal < $threshold) {
                $buyMore = $threshold - $vendorTotal;
                $message = str_replace('[amount remaining]', $this->formatPrice($buyMore), $addToCartUnderMessage);
            } else {
                $message = $addToCartReachedMessage;
            }

            $messageCollection = $this->messageManager->getMessages(false);
            if (!empty($messageCollection->getItemsByType('success'))) {
                $this->messageManager->addSuccessMessage($messageCollection->getLastAddedMessage()->getText() . '  ' . $message);

            }
        }
    }

    public function formatPrice($price) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of Object Manager
        $priceHelper = $objectManager->create('Magento\Framework\Pricing\Helper\Data'); // Instance of Pricing Helper

        return $priceHelper->currency($price, true, false);
    }
}