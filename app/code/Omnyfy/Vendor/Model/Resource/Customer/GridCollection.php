<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 13/7/17
 * Time: 11:48 AM
 */
namespace Omnyfy\Vendor\Model\Resource\Customer;


use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\State as AppState;
use Omnyfy\Vendor\Helper\User;
use Magento\Backend\Model\Auth\Session as AdminSession;

class GridCollection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    protected $appState;

    protected $backendSession;

    protected $_userHelper;

    protected $_adminSession;

    public function __construct(
        AppState $appState,
        BackendSession $backendSession,
        User $userHelper,
        AdminSession $adminSession,
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'customer_grid_flat',
        $resourceModel = '\Magento\Customer\Model\ResourceModel\Customer'
    )
    {
        $this->appState = $appState;
        $this->backendSession = $backendSession;
        $this->_userHelper = $userHelper;
        $this->_adminSession = $adminSession;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _renderFiltersBefore() {
        parent::_renderFiltersBefore();

        if (FrontNameResolver::AREA_CODE != $this->appState->getAreaCode()) {
            return;
        }

        $vendorInfo = $this->backendSession->getVendorInfo();

        $currentUser = $this->_adminSession->getUser();

        $userVendor = $this->_userHelper->getUserVendor($currentUser->getUserId());

        // If this is set, this will override what is set inside the vendor
        $userStores = $this->_userHelper->getUserStores($currentUser->getUserId());

        if (empty($vendorInfo) && (!$userStores || in_array(0, $userStores))) {
            $this->_eventManager->dispatch('omnyfy_vendor_customer_grid_render_filter_before', ['collection' => $this]);
            return;
        }

        if (!empty($vendorInfo) && (!$userStores || in_array(0, $userStores)) && !$vendorInfo['vendor_id']) {
            $this->_eventManager->dispatch('omnyfy_vendor_customer_grid_render_filter_before', ['collection' => $this]);
            return;
        }


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

        if (empty($vendorInfo)) {
            if ($userStores) {
                $this->addFieldToFilter('website_id', ['in' => $userStores]);
            }
        }

        if (!empty($vendorInfo)) {
            if ($userStores) {
                $this->addFieldToFilter('website_id', ['in' => $userStores]);
            } else {
                $this->addFieldToFilter('website_id', ['in' => $vendorInfo['website_ids']]);
            }
        }

        if (!empty($vendorInfo)) {
            if ($vendorInfo['vendor_id']) {
                $cvTable = 'omnyfy_vendor_vendor_customer';
                $this->addFieldToFilter(
                    'entity_id',
                    [
                        'in' => new \Zend_Db_Expr(
                            'SELECT customer_id FROM ' . $cvTable . ' WHERE vendor_id=' . $vendorInfo['vendor_id']
                        )
                    ]
                );
            }
        }

        $this->_logger->debug('filtered customer collection', $vendorInfo);

        $this->_eventManager->dispatch('omnyfy_vendor_customer_grid_render_filter_before', ['collection' => $this]);
    }

    public function getAllIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');

        if (FrontNameResolver::AREA_CODE == $this->appState->getAreaCode()) {
            $vendorInfo = $this->backendSession->getVendorInfo();

            if (!empty($vendorInfo)) {
                $idsSelect->where('website_id IN (?)', $vendorInfo['website_ids']);
                $cvTable = 'omnyfy_vendor_vendor_customer';
                $idsSelect->where(new \Zend_Db_Expr('entity_id IN (SELECT customer_id FROM '. $cvTable . ' WHERE vendor_id=' .$vendorInfo['vendor_id'].')'));
            }
            $this->_eventManager->dispatch('omnyfy_vendor_customer_grid_get_all_ids_before', ['select' => $idsSelect, 'collection' => $this]);
        }

        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }
}