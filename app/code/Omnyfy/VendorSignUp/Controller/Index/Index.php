<?php
namespace Omnyfy\VendorSignUp\Controller\Index;

use Magento\Framework\View\Result\PageFactory;

class Index extends \Magento\Framework\App\Action\Action
{

	/**
     * @var PageFactory
     */
    protected $resultPageFactory;

    protected $vendorTypeRepository;

    protected $_registry;

    protected $_storeManager;

    protected $_helper;
    
    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        PageFactory $resultPageFactory,
        \Omnyfy\Vendor\Api\VendorTypeRepositoryInterface $vendorTypeRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Omnyfy\VendorSignUp\Helper\Data $helper
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->vendorTypeRepository = $vendorTypeRepository;
        $this->_registry = $coreRegistry;
        $this->_storeManager = $storeManager;
        $this->_helper = $helper;
        parent::__construct($context);
    }

    public function execute() {
        //vendor_type_id is a required parameter to show the form
        $vendorTypeId = $this->getRequest()->getParam('type_id', null);
        if (empty($vendorTypeId)) {
            //TODO: redirect to type selection page
            $this->_redirect($this->getBackUrl());
        }
        try {						
            $vendorType = $this->vendorTypeRepository->getById($vendorTypeId);			

            //save vendor type into registry
            $this->_registry->register('current_omnyfy_vendor_type', $vendorType);

            $planId = $this->getRequest()->getParam('plan_id', null);
            if (!is_null($planId)) {
                $this->_eventManager->dispatch('sign_up_form_load_before',
                    [
                        'plan_id' => $planId,
                        'vendor_type' => $vendorType
                    ]
                );
            }

        }catch (\Exception $e) {
            $this->_redirect($this->getBackUrl());
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set('Vendor Sign Up');
        return $resultPage;
    }

    protected function getBackUrl($defaultUrl = null)
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl && $this->_isInternalUrl($returnUrl)) {
            $this->messageManager->getMessages()->clear();
            return $returnUrl;
        }

        $configUrl = $this->_helper->getReturnUrl();
        if (!empty($configUrl)) {
            return $configUrl;
        }

        if (empty($defaultUrl)) {
            return $this->_url->getUrl('/');
        }

        return $defaultUrl;
    }

    protected function _isInternalUrl($url)
    {
        if (strpos($url, 'http') === false) {
            return false;
        }

        $store = $this->_storeManager->getStore();
        $unSecure = strpos($url, $store->getBaseUrl()) === 0;
        $secure = strpos($url, $store->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK, true)) === 0;
        return $unSecure || $secure;
    }
}
