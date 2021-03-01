<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 13/7/17
 * Time: 11:25 AM
 */
namespace Omnyfy\Vendor\Model\Resource\Invoice;

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

    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        AppState $appState,
        BackendSession $backendSession,
        User $userHelper,
        AdminSession $adminSession,
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'sales_invoice_grid',
        $resourceModel = \Magento\Sales\Model\ResourceModel\Order\Invoice::class
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
            return;
        }

        if (!empty($vendorInfo) && (!$userStores || in_array(0, $userStores)) && !$vendorInfo['vendor_id']) {
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
            }
        }

//        $this->_logger->debug('here2: '. get_class($this));

        // if vendor is empty but a store is set on user, set collection
        if (empty($vendorInfo) && $userStores) {
            $this->addFieldToFilter('store_id', ['in' => $userStores]);
        }
        // if vendor info is not empty
        elseif(!empty($vendorInfo)) {
            // Stores set in user overrides the vendor settings
            if ($userStores) {
                $this->addFieldToFilter('store_id', ['in' => $userStores]);
            } else {
                $this->addFieldToFilter('store_id', ['in' => $vendorInfo['store_ids']]);
            }

            $ivTable = 'omnyfy_vendor_vendor_invoice';
            if ($vendorInfo['vendor_id']) {
                $this->addFieldToFilter(
                    'entity_id',
                    [
                        'in' => new \Zend_Db_Expr(
                            'SELECT invoice_id FROM ' . $ivTable . ' WHERE vendor_id=' . $vendorInfo['vendor_id']
                        )
                    ]
                );

                $this->getSelect()->joinLeft(
                    ['total' => $this->getTable('omnyfy_vendor_invoice_total')],
                    'main_table.entity_id = total.invoice_id AND total.vendor_id=' . $vendorInfo['vendor_id'],
                    ['grand_total' => 'total.grand_total', 'base_grand_total' => 'total.base_grand_total']
                );

                $this->_logger->debug('filtered invoice collection', $vendorInfo);
            }
        }
    }
}