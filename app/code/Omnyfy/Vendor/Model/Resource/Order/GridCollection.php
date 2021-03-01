<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 12/7/17
 * Time: 4:36 PM
 */

namespace Omnyfy\Vendor\Model\Resource\Order;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\State;
Use Omnyfy\Vendor\Helper\User;
Use Magento\Backend\Model\Auth\Session as AdminSession;

class GridCollection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    protected $backendSession;

    protected $appState;

    protected $_userHelper;

    protected $_adminSession;

    public function __construct(
        State $appState,
        BackendSession $backendSession,
        User $userHelper,
        AdminSession $adminSession,
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'sales_order_grid',
        $resourceModel = '\Magento\Sales\Model\ResourceModel\Order'
    ) {
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

        // If vendor info is empty and no stores are set on user, display everything
        if (empty($vendorInfo) && (!$userStores || in_array(0, $userStores))) {
            $this->_eventManager->dispatch('omnyfy_vendor_order_grid_render_filter_before', ['collection' => $this]);
            return;
        }

        if (!empty($vendorInfo) && (!$userStores || in_array(0, $userStores)) && !$vendorInfo['vendor_id']) {
            $this->_eventManager->dispatch('omnyfy_vendor_order_grid_render_filter_before', ['collection' => $this]);
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
        // $this->_logger->debug('here2: '. get_class($this));

        // if vendor is empty but a store is set on user, set collection
        if (empty($vendorInfo) && $userStores) {
            $this->addFieldToFilter('store_id', ['in' => $userStores]);
        }
        // if vendor info is not empty
        elseif (!empty($vendorInfo)) {
            // Stores set in user overrides the vendor settings
            if ($userStores) {
                $this->addFieldToFilter('store_id', ['in' => $userStores]);
            } else {
                $this->addFieldToFilter('store_id', ['in' => $vendorInfo['store_ids']]);
            }

            if ($vendorInfo['vendor_id']) {
                $ovTable = 'omnyfy_vendor_vendor_order';
                $this->addFieldToFilter(
                    'entity_id',
                    [
                        'in' => new \Zend_Db_Expr('SELECT order_id FROM ' . $ovTable . ' WHERE vendor_id=' . $vendorInfo['vendor_id'])
                    ]
                );

                $selectedQuery = $this->getSelect()->joinLeft(
                    ['total' => $this->getTable('omnyfy_vendor_order_total')],
                    'main_table.entity_id = total.order_id AND total.vendor_id=' . $vendorInfo['vendor_id'],
                    ['grand_total' => 'total.grand_total', 'base_grand_total' => 'total.base_grand_total']
                );

                $this->_logger->debug('filtered order collection', $vendorInfo);
            }

            $this->_eventManager->dispatch('omnyfy_vendor_order_grid_render_filter_before', ['collection' => $this]);
        }
    }

    public function getAllIds() {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');

        if (FrontNameResolver::AREA_CODE == $this->appState->getAreaCode()) {
            $vendorInfo = $this->backendSession->getVendorInfo();

            if (!empty($vendorInfo)) {
                $idsSelect->where('store_id IN (?)', $vendorInfo['store_ids']);
                $ovTable = 'omnyfy_vendor_vendor_order';
                $idsSelect->where(new \Zend_Db_Expr('entity_id IN (SELECT order_id FROM ' . $ovTable . ' WHERE vendor_id=' . $vendorInfo['vendor_id'] . ')'));
            }
            $this->_eventManager->dispatch('omnyfy_vendor_order_grid_get_all_ids_before', ['select' => $idsSelect, 'collection' => $this]);
        }
        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }

    public function _renderOrders()
    {
        $vendorInfo = $this->backendSession->getVendorInfo();

        if (empty($vendorInfo)) {
            return parent::_renderOrders();
        }

        if (!$this->_isOrdersRendered) {
            foreach ($this->_orders as $field => $direction) {
                if ('grand_total'=== $field ) {
                    $field = 'total.grand_total';
                }
                if ('base_grand_total' === $field) {
                    $field = 'total.base_grand_total';
                }
                $this->_select->order(new \Zend_Db_Expr($field . ' ' . $direction));
            }
            $this->_isOrdersRendered = true;
        }

        return $this;
    }
}