<?php

namespace Omnyfy\Cms\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Admin extends AbstractHelper {

    protected $storeManager;
    protected $objectManager;

    /**
     * Path to store config where count of events posts per page is stored
     *
     * @var string
     */
    const XML_PATH_ITEMS_PER_PAGE = 1;
    const XML_PATH = 'mfcms/service_category/';

    public function __construct(Context $context, ObjectManagerInterface $objectManager, StoreManagerInterface $storeManager
    ) {
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function getConfigValue($field, $storeId = null) {
        return $this->scopeConfig->getValue(
                        $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    public function getGeneralConfig($code, $storeId = null) {
        return $this->getConfigValue(self::XML_PATH . $code, $storeId);
    }

    public function getConfig($config_path) {
        return $this->scopeConfig->getValue(
                        $config_path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    
    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }
}
