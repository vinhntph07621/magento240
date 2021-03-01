<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-email-designer
 * @version   1.1.45
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */


namespace Mirasvit\EmailDesigner\Service\TemplateEngine\Liquid\Variable;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\View\Element\BlockFactory;
use Mirasvit\EmailDesigner\Service\TemplateEngine\Liquid\Variable\Product as VariableProduct;

class Item extends AbstractVariable
{
    /**
     * List of classes for this variable.
     *
     * @var string[]
     */
    protected $supportedTypes = [
        'Magento\Sales\Model\Order\Item',
        'Magento\Quote\Model\Quote\Item',
        //'Mirasvit\EmailDesigner\Service\TemplateEngine\Liquid\Variable\Product'
    ];

    /**
     * List of allowed methods.
     *
     * @var string[]
     */
    protected $whitelist = [
        'getQtyOrdered',
    ];

    /**
     * @var Product
     */
    private $productVariable;

    /**
     * @var Quote
     */
    private $quoteVariable;

    /**
     * @var Order
     */
    private $orderVariable;

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * @var Store
     */
    private $storeVariable;

    /**
     * @var VariableProduct
     */
    private $variableProduct;

    /**
     * @var BlockFactory
     */
    private $blockFactory;

    /**
     * Item constructor.
     * @param BlockFactory $blockFactory
     * @param Store $storeVariable
     * @param ScopeConfigInterface $config
     * @param Order $orderVariable
     * @param Quote $quoteVariable
     * @param Product $productVariable
     */
    public function __construct(
        BlockFactory         $blockFactory,
        Store                $storeVariable,
        ScopeConfigInterface $config,
        Order                $orderVariable,
        Quote                $quoteVariable,
        Product              $productVariable,
        VariableProduct      $variableProduct
    ) {
        parent::__construct();

        $this->orderVariable   = $orderVariable;
        $this->quoteVariable   = $quoteVariable;
        $this->productVariable = $productVariable;
        $this->config          = $config;
        $this->storeVariable   = $storeVariable;
        $this->blockFactory    = $blockFactory;
        $this->variableProduct = $variableProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function getVariableName()
    {
        return __('Order/Quote Item');
    }

    /** VARIABLES **/

    /**
     * Get name
     *
     * @return float
     */
    public function getName()
    {
        $result = '';
        if ($item = $this->context->getData('item')) {
            $result = $item->getName();
        }

        return $result;
    }

    /**
     * Get price including tax
     *
     * @filter | format_price
     *
     * @return float
     */
    public function getPriceInclTax()
    {
        $result = '';
        if ($item = $this->context->getData('item')) {
            $result = $item->getPriceInclTax();
        }

        return $result;
    }

    /**
     * Get price
     *
     * @filter | format_price
     *
     * @return float
     */
    public function getPrice()
    {
        $result = '';
        if ($item = $this->context->getData('item')) {
            $result = $item->getPrice();
        }

        return $result;
    }

    /**
     * Get price HTML
     *
     * @return string
     */
    public function getPriceHtml()
    {
        $result = '';
        if ($item = $this->context->getData('item')) {
            /** @var \Magento\Weee\Block\Item\Price\Renderer $priceRender */
            $priceRender = $this->blockFactory->createBlock(\Magento\Weee\Block\Item\Price\Renderer::class);
            $result = $priceRender->setArea('frontend')
                ->setTemplate('item/price/unit.phtml')
                ->setZone('cart')
                ->setItem($item)
                ->toHtml();
        }

        return $result;
    }

    /**
     * Get product image url
     *
     * @namespace item
     * @filter | resize: 'image', 300
     *
     * @return string
     */
    public function getImage()
    {
        $result = '';
        if ($item = $this->context->getData('item')) {
            $result = $this->getProductImage($item);
        }

        return $result;
    }

    /**
     * Get product url
     *
     * @namespace item.product
     *
     * @return string
     */
    public function getProductUrl()
    {
        $result = '';
        if ($item = $this->context->getData('item')) {
            /** @var \Magento\Quote\Model\Quote\Item $item */
            $productId = $item->getProduct()->getId();
            $result = $this->variableProduct->getProductByStore($productId)->getProductUrl();
        }

        return $result;
    }

    /**
     * Get item quantity
     *
     * @return int
     */
    public function getQty()
    {
        $result = '';
        if ($item = $this->context->getData('item')) {
            if (!$result = $item->getQty()) {
                $options = $item->getProductOptions();
                $result = (int)$options['info_buyRequest']['qty'];
            }
        }

        return $result;
    }

    /**
     * Get amount of products in the cart
     *
     * @return int
     */
    public function getCartQty()
    {
        $result = '';
        if ($items = $this->getAllVisibleItems()) {
            $result = count($items);
        }

        return $result;
    }

    /**
     * Get array of all items that can be displayed
     *
     * @namespace this
     *
     * @return \Magento\Sales\Model\Order\Item[]
     */
    public function getAllVisibleItems()
    {
        $items = [];
        if ($this->context->getData('order_id')) {
            $order = $this->orderVariable->setContext($this->context)->getOrder();
            $items = $order->getAllVisibleItems();
        } elseif ($this->context->getData('quote_id')) {
            $quote = $this->quoteVariable->setContext($this->context)->getQuote();
            $items = $quote->getAllVisibleItems();
        }

        return $items;
    }

    /**
     * Get first enabled item from the quote/order
     *
     * @namespace this
     *
     * @return \Magento\Sales\Model\Order\Item[]
     */
    public function getFirstVisibleItem()
    {
        $itemArray = [];
        $items = $this->getAllVisibleItems();
        if (!empty($items)) {
            foreach ($items as $item) {
                $product  = $item->getProduct();
                if ($product->getStatus() == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED) {
                    $itemArray[] = $product;
                    break;
                }
            }
        }

        return $itemArray;
    }

    /**
     * @param AbstractModel|\Magento\Quote\Model\Quote\Item|\Magento\Sales\Model\Order\Item $item
     *
     * @return string
     */
    private function getProductImage(AbstractModel $item)
    {
        if (!$item->getProduct()) {
            return null;
        }

        $product  = $item->getProduct();
        $product  = $this->variableProduct->getProductByStore($product->getId());
        /** @var \Magento\Catalog\Model\ResourceModel\Product $resource */
        $resource = $product->getResource();
        $store    = $this->storeVariable->setContext($this->context)->getStore();
        $image    = $resource->getAttributeRawValue($product->getId(), 'small_image', $store);

        // configurable image
        if ($item->getProductType() === 'configurable') {
            $configProductImage = $this->config->getValue(
                'checkout/cart/configurable_product_image',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store
            );

            if ($configProductImage === 'itself') {
                $child = $product->getIdBySku($item->getSku());
                $cfgImage = $resource->getAttributeRawValue($child, 'small_image', $store);
                if ($cfgImage && $cfgImage !== 'no_selection') {
                    $image = $cfgImage;
                }
            }
        }

        // grouped image
        $groupedProductImage = $this->config->getValue(
            'checkout/cart/grouped_product_image',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        if ($groupedProductImage === 'parent') {
            $options = $product->getTypeInstance()->getOrderOptions($product);

            if (isset($options['super_product_config'])
                && $options['super_product_config']['product_type'] === 'grouped'
            ) {
                $parent = $options['super_product_config']['product_id'];
                $groupedImage = $resource->getAttributeRawValue($parent, 'small_image', $store);
                if ($groupedImage && $groupedImage !== 'no_selection') {
                    $image = $groupedImage;
                }
            }
        }

        if (!$image || $image === 'no_selection') {
            $image = null;
        }

        return $image;
    }
}
