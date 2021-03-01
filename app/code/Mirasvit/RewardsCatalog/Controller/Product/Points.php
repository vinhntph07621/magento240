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
 * @package   mirasvit/module-rewards
 * @version   3.0.21
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\RewardsCatalog\Controller\Product;

use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Action\Action;

class Points extends Action
{
    /**
     * @var \Mirasvit\Rewards\Model\Config
     */
    protected $config;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Mirasvit\RewardsCatalog\Helper\EarnProductPage
     */
    protected $earnProductPageHelper;
    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var \Mirasvit\Rewards\Helper\Data
     */
    protected $rewardsDataHelper;
    /**
     * @var \Mirasvit\RewardsCatalog\Helper\Spend
     */
    protected $spendHelper;
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Mirasvit\Rewards\Helper\Data                    $rewardsDataHelper,
        \Mirasvit\RewardsCatalog\Helper\EarnProductPage  $earnProductPageHelper,
        \Mirasvit\RewardsCatalog\Helper\Spend            $spendHelper,
        \Mirasvit\Rewards\Model\Config                   $config,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface  $productRepository,
        \Magento\Customer\Model\Session                  $customerSession,
        \Magento\Store\Model\StoreManagerInterface       $storeManager,
        \Magento\Framework\App\Action\Context            $context
    ) {
        $this->config                = $config;
        $this->rewardsDataHelper     = $rewardsDataHelper;
        $this->earnProductPageHelper = $earnProductPageHelper;
        $this->spendHelper           = $spendHelper;
        $this->productRepository     = $productRepository;
        $this->customerSession       = $customerSession;
        $this->storeManager          = $storeManager;
        $this->resultJsonFactory     = $resultJsonFactory;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Currency_Exception
     */
    public function execute()
    {
        $response = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $isMoney  = $this->config->getGeneralIsDisplayProductPointsAsMoney();
        $products = $this->getRequest()->getParams();
        $result = [];
        foreach ($products as $k => $data) {
            $product      = $this->productRepository->getById((int)$data['product_id']);
            $productPrice = (float)$data['price'] * (int)$data['qty'];
            $customer     = $this->customerSession->getCustomer();
            $websiteId    = $this->storeManager->getWebsite()->getId();

            if ($product->getTypeId() == Configurable::TYPE_CODE) {
                $product->setProduct($product);
                $children = [
                    $product
                ];
                $product->setChildren($children);
            }

            $product->setRewardsQty((int)$data['qty']);
            $points = $this->earnProductPageHelper->getProductPagePoints($product, $productPrice, $customer, $websiteId);
            if ($isMoney) {
                $money = $this->spendHelper->getProductPointsAsMoney($points, $productPrice, $customer, $websiteId);
                $label = __('Possible discount %1', $money);
            } else {
                $label = __('Earn %1', $this->rewardsDataHelper->formatPoints($points));
            }

            // $k - key of js object. Do not change.
            $result[$k] = [
                'points'     => $points,
                'label'      => $label,
                'product_id' => $data['product_id'],
            ];
        }

        return $response->setData($result);
    }
}
