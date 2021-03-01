<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 13/7/17
 * Time: 12:29 PM
 */
namespace Omnyfy\Vendor\Model\Resource\Shipment;


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
        $mainTable = 'sales_shipment_grid',
        $resourceModel = \Magento\Sales\Model\ResourceModel\Order\Shipment::class
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

        $this->_logger->debug('here2: '. get_class($this));

        if (empty($vendorInfo) && $userStores) {
            $this->addFieldToFilter('store_id', ['in' => $userStores]);
        }

        elseif (!empty($vendorInfo)) {
            if ($userStores) {
                $this->addFieldToFilter('store_id', ['in' => $userStores]);
            } else {
                $this->addFieldToFilter('store_id', ['in' => $vendorInfo['store_ids']]);
            }
            if ($vendorInfo['vendor_id']) {
                $this->addFieldToFilter('vendor_id', $vendorInfo['vendor_id']);
            }
        }

        $this->_logger->debug('filtered shipment collection', $vendorInfo);
    }
}
