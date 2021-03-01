<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Controller;

use Magento\Catalog\Model\Vendor as CatalogVendor;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Omnyfy\VendorReview\Model\Review;

/**
 * Review controller
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
abstract class Vendor extends \Magento\Framework\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * Customer session model
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * Generic session
     *
     * @var \Magento\Framework\Session\Generic
     */
    protected $reviewSession;

    /**
     * Catalog catgory model
     *
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * Logger
     *
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Catalog vendor model
     *
     * @var \Omnyfy\Vendor\Api\VendorRepositoryInterface
     */
    protected $vendorRepository;

    /**
     * Review model
     *
     * @var \Omnyfy\VendorReview\Model\ReviewFactory
     */
    protected $reviewFactory;

    /**
     * Rating model
     *
     * @var \Omnyfy\VendorReview\Model\RatingFactory
     */
    protected $ratingFactory;

    /**
     * Catalog design model
     *
     * @var \Magento\Catalog\Model\Design
     */
    protected $catalogDesign;

    /**
     * Core model store manager interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Core form key validator
     *
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository
     * @param \Omnyfy\VendorReview\Model\ReviewFactory $reviewFactory
     * @param \Omnyfy\VendorReview\Model\RatingFactory $ratingFactory
     * @param \Magento\Catalog\Model\Design $catalogDesign
     * @param \Magento\Framework\Session\Generic $reviewSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Psr\Log\LoggerInterface $logger,
        \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository,
        \Omnyfy\VendorReview\Model\ReviewFactory $reviewFactory,
        \Omnyfy\VendorReview\Model\RatingFactory $ratingFactory,
        \Magento\Catalog\Model\Design $catalogDesign,
        \Magento\Framework\Session\Generic $reviewSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
    ) {
        $this->storeManager = $storeManager;
        $this->coreRegistry = $coreRegistry;
        $this->customerSession = $customerSession;
        $this->reviewSession = $reviewSession;
        $this->categoryRepository = $categoryRepository;
        $this->logger = $logger;
        $this->vendorRepository = $vendorRepository;
        $this->reviewFactory = $reviewFactory;
        $this->ratingFactory = $ratingFactory;
        $this->catalogDesign = $catalogDesign;
        $this->formKeyValidator = $formKeyValidator;

        parent::__construct($context);
    }

    /**
     * Dispatch request
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $allowGuest = $this->_objectManager->get('Omnyfy\VendorReview\Helper\Data')->getIsGuestAllowToWrite();
        if (!$request->isDispatched()) {
            return parent::dispatch($request);
        }

        if (!$allowGuest && $request->getActionName() == 'post' && $request->isPost()) {
            if (!$this->customerSession->isLoggedIn()) {
                $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
                $this->customerSession->setBeforeAuthUrl($this->_url->getUrl('*/*/*', ['_current' => true]));
                $this->_reviewSession->setFormData(
                    $request->getPostValue()
                )->setRedirectUrl(
                    $this->_redirect->getRefererUrl()
                );
                $this->getResponse()->setRedirect(
                    $this->_objectManager->get('Magento\Customer\Model\Url')->getLoginUrl()
                );
            }
        }

        return parent::dispatch($request);
    }

    /**
     * Initialize and check vendor
     *
     * @return \Magento\Catalog\Model\Vendor|bool
     */
    protected function initVendor()
    {
        $this->_eventManager->dispatch('review_controller_vendor_init_before', ['controller_action' => $this]);
        $categoryId = (int)$this->getRequest()->getParam('category', false);
        $vendorId = (int)$this->getRequest()->getParam('id');

        $vendor = $this->loadVendor($vendorId);
        if (!$vendor) {
            return false;
        }

        if ($categoryId) {
            $category = $this->categoryRepository->get($categoryId);
            $this->coreRegistry->register('current_category', $category);
        }

        try {
            $this->_eventManager->dispatch('review_controller_vendor_init', ['vendor' => $vendor]);
            $this->_eventManager->dispatch(
                'review_controller_vendor_init_after',
                ['vendor' => $vendor, 'controller_action' => $this]
            );
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->logger->critical($e);
            return false;
        }

        return $vendor;
    }

    /**
     * Load vendor model with data by passed id.
     * Return false if vendor was not loaded or has incorrect status.
     *
     * @param int $vendorId
     * @return bool|CatalogVendor
     */
    protected function loadVendor($vendorId)
    {
        if (!$vendorId) {
            return false;
        }

        try {
            $vendor = $this->vendorRepository->getById($vendorId);

            if (!$vendor) {
                throw new NoSuchEntityException();
            }
        } catch (NoSuchEntityException $noEntityException) {
            return false;
        }

        $this->coreRegistry->register('current_vendor', $vendor);
        $this->coreRegistry->register('vendor', $vendor);

        return $vendor;
    }
}
