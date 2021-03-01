<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 13/7/17
 * Time: 10:52 AM
 */

namespace Omnyfy\Vendor\Model\Resource\Product;

use Magento\Backend\Model\Session as BackendSession;
use Magento\Customer\Api\GroupManagementInterface;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\State;
Use Omnyfy\Vendor\Helper\User;
Use Magento\Backend\Model\Auth\Session as AdminSession;

class Collection extends \Magento\Catalog\Model\ResourceModel\Product\Collection
{
    protected $backendSession;

    protected $appState;

    protected $request;

    protected $_userHelper;

    protected $_adminSession;

    public function __construct(
        State $appState,
        BackendSession $backendSession,
        User $userHelper,
        AdminSession $adminSession,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        \Magento\Catalog\Model\ResourceModel\Helper $resourceHelper,
        \Magento\Framework\Validator\UniversalFactory $universalFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $catalogProductFlatState,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\Product\OptionFactory $productOptionFactory,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        GroupManagementInterface $groupManagement,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null
    )
    {
        $this->appState = $appState;
        $this->backendSession = $backendSession;
        $this->_userHelper = $userHelper;
        $this->_adminSession = $adminSession;
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $eavConfig, $resource, $eavEntityFactory, $resourceHelper, $universalFactory, $storeManager, $moduleManager, $catalogProductFlatState, $scopeConfig, $productOptionFactory, $catalogUrl, $localeDate, $customerSession, $dateTime, $groupManagement, $connection);
    }

    protected function _renderFiltersBefore() {
        parent::_renderFiltersBefore();

        if (FrontNameResolver::AREA_CODE != $this->appState->getAreaCode()) {
            return;
        }

        $vendorInfo = $this->backendSession->getVendorInfo();

        $currentUser = $this->_adminSession->getUser();

        if (!$currentUser) {
            return;
        }

        $userVendor = $this->_userHelper->getUserVendor($currentUser->getUserId());

        // If this is set, this will override what is set inside the vendor
        $userStores = $this->_userHelper->getUserStores($currentUser->getUserId());

        // If vendor info is empty and no stores are set on user, display everything
        if (empty($vendorInfo) && (!$userStores || in_array(0, $userStores))) {
            return;
        }

        if (!empty($vendorInfo) && (!$userStores || in_array(0, $userStores)) && !$vendorInfo['vendor_id']) {
            return;
        }

        // If vendor info is not empty
        if (!empty($vendorInfo)) {
            if (empty($vendorInfo['website_ids'])) {
                $vendorInfo['website_ids'] = [-1];
            }
            if (empty($vendorInfo['store_ids'])) {
                $vendorInfo['store_ids'] = [-1];
            }
            if (empty($vendorInfo['profile_ids'])) {
                $vendorInfo['profile_ids'] = [-1];
            }
            if (empty($vendorInfo['location_ids'])) {
                $vendorInfo['location_ids'] = [-1];
            }
            if (empty($vendorInfo['vendor_id'])) {
                $vendorInfo['vendor_id'] = 0;
                //$vendorInfo['vendor_id'] = 1;
            }
        }

        $this->_logger->debug('here2: '. get_class($this));

        if (empty($vendorInfo) && $userStores) {
            $this->addWebsiteFilter($userStores);
        }

        if (!empty($vendorInfo)) {
            if ($userStores) {
                $this->addWebsiteFilter($userStores);
            } else {
                $this->addWebsiteFilter($vendorInfo['website_ids']);
            }
            //add MO vendor ids to sub select
            $vendorIds = [
                $vendorInfo['vendor_id']
            ];

            $includeMoVendorIds = $this->_scopeConfig->isSetFlag(\Omnyfy\Vendor\Model\Config::XML_PATH_INCLUDE_MO_PRODUCTS);
            if ($includeMoVendorIds) {
                //ONLY selected vendor types can see MO products
                $typeIdStr = $this->_scopeConfig->getValue(\Omnyfy\Vendor\Model\Config::XML_PATH_VENDOR_TYPE_IDS);
                $typeIds = explode(',', $typeIdStr);
                if (in_array($vendorInfo['type_id'], $typeIds)) {
                    $str = $this->_scopeConfig->getValue(\Omnyfy\Vendor\Model\Config::XML_PATH_MO_VENDOR_IDS);
                    $moVendorIds = explode(',', $str);
                    if (!empty($moVendorIds)) {
                        $vendorIds = array_merge($vendorIds, $moVendorIds);
                    }
                }
            }

            $pvTable = 'omnyfy_vendor_vendor_product';
            if ($vendorInfo['vendor_id']) {
                $select = $this->_conn->select()
                    ->from($this->_conn->getTableName($pvTable), ['product_id'])
                    ->where('vendor_id in (?)', $vendorIds);
            }

            $action = $this->request->getActionName();
            if ($vendorInfo['vendor_id']) {
                /**
                 * Added checking to skip this filter during Product Save since it causes an issue - Qty not being saved on product creation.
                 */
                if ($action !== 'save') {
                    $this->addFieldToFilter(
                        'entity_id',
                        [
                            'in' => $select
                        ]
                    );
                }
            }

            $this->_logger->debug('filtered product collection', $vendorInfo);
        }
    }

    public function _beforeLoad()
    {
        $this->_eventManager->dispatch('omnyfy_product_collection_before_load', array('collection' => $this));

        return parent::_beforeLoad();
    }
}