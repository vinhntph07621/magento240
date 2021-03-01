<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Omnyfy\VendorReview\Block;

use Magento\Catalog\Model\Vendor;
use Magento\Customer\Model\Context;
use Magento\Customer\Model\Url;
use Omnyfy\VendorReview\Model\ResourceModel\Rating\Collection as RatingCollection;

/**
 * Review form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Form extends \Magento\Framework\View\Element\Template
{
    /**
     * Review data
     *
     * @var \Omnyfy\VendorReview\Helper\Data
     */
    protected $_reviewData = null;

    /**
     * Catalog vendor model
     *
     * @var \Omnyfy\Vendor\Api\VendorRepositoryInterface
     */
    protected $vendorRepository;

    /**
     * Rating model
     *
     * @var \Omnyfy\VendorReview\Model\RatingFactory
     */
    protected $_ratingFactory;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * Message manager interface
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $customerUrl;

    /**
     * @var array
     */
    protected $jsLayout;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Omnyfy\VendorReview\Helper\Data $reviewData
     * @param \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository
     * @param \Omnyfy\VendorReview\Model\RatingFactory $ratingFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Omnyfy\VendorReview\Helper\Data $reviewData,
        \Omnyfy\Vendor\Api\VendorRepositoryInterface $vendorRepository,
        \Omnyfy\VendorReview\Model\RatingFactory $ratingFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Url $customerUrl,
        array $data = []
    ) {
        $this->urlEncoder = $urlEncoder;
        $this->_reviewData = $reviewData;
        $this->vendorRepository = $vendorRepository;
        $this->_ratingFactory = $ratingFactory;
        $this->messageManager = $messageManager;
        $this->httpContext = $httpContext;
        $this->customerUrl = $customerUrl;
        parent::__construct($context, $data);
        $this->jsLayout = isset($data['jsLayout']) ? $data['jsLayout'] : [];
    }

    /**
     * Initialize review form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setAllowWriteReviewFlag(
            $this->httpContext->getValue(Context::CONTEXT_AUTH)
            || $this->_reviewData->getIsGuestAllowToWrite()
        );
        if (!$this->getAllowWriteReviewFlag()) {
            $queryParam = $this->urlEncoder->encode(
                $this->getUrl('*/*/*', ['_current' => true]) . '#review-form'
            );
            $this->setLoginLink(
                $this->getUrl(
                    'customer/account/login/',
                    [Url::REFERER_QUERY_PARAM_NAME => $queryParam]
                )
            );
        }

        $this->setTemplate('form.phtml');
    }

    /**
     * @return string
     */
    public function getJsLayout()
    {
        return \Zend_Json::encode($this->jsLayout);
    }

    /**
     * Get vendor info
     *
     * @return Vendor
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getVendorInfo()
    {
        return $this->vendorRepository->getById(
            $this->getVendorId(),
            false,
            $this->_storeManager->getStore()->getId()
        );
    }

    /**
     * Get review vendor post action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->getUrl(
            'vendorreview/vendor/post',
            [
                '_secure' => $this->getRequest()->isSecure(),
                'id' => $this->getVendorId(),
            ]
        );
    }

    /**
     * Get collection of ratings
     *
     * @return RatingCollection
     */
    public function getRatings()
    {
        return $this->_ratingFactory->create()->getResourceCollection()->addEntityFilter(
            'vendor'
        )->setPositionOrder()->addRatingPerStoreName(
            $this->_storeManager->getStore()->getId()
        )->setStoreFilter(
            $this->_storeManager->getStore()->getId()
        )->setActiveFilter(
            true
        )->load()->addOptionToItems();
    }

    /**
     * Return register URL
     *
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->customerUrl->getRegisterUrl();
    }

    /**
     * Get review vendor id
     *
     * @return int
     */
    protected function getVendorId()
    {
        return $this->getRequest()->getParam('id', false);
    }
}
