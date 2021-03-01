<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 17/7/17
 * Time: 3:35 PM
 */

namespace Omnyfy\Vendor\Model\Resource\Order;

use Magento\Backend\Model\Session as BackendSession;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot;

class ItemCollection extends \Magento\Sales\Model\ResourceModel\Order\Item\Collection
{
    protected $backendSession;

    protected $appState;

    public function __construct(
        AppState $appState,
        BackendSession $backendSession,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        Snapshot $entitySnapshot,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    )
    {
        $this->backendSession = $backendSession;

        $this->appState = $appState;

        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $entitySnapshot, $connection, $resource);
    }

    protected function _renderFiltersBefore() {
        parent::_renderFiltersBefore();

        if ($allItems = $this->getFlag('all_items'))
            return;

        if (FrontNameResolver::AREA_CODE != $this->appState->getAreaCode()) {
            return;
        }

        $vendorInfo = $this->backendSession->getVendorInfo();

        if (empty($vendorInfo)) {
            return;
        }

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

        if ($vendorInfo['vendor_id'] == 0)
            return;

        $this->_logger->debug('here2: '. get_class($this));

        $this->addFieldToFilter('store_id', ['in' => $vendorInfo['store_ids']]);

        $this->addFieldToFilter('vendor_id', $vendorInfo['vendor_id']);
        //2019-10-02 16:23 Jing Xiao,
        //Since we decoupled vendor_id and location_id relation in sales_order_item Table,
        // no point to filter by location_id again
        //$this->addFieldToFilter('location_id', ['in' => $vendorInfo['location_ids']]);

        $this->_logger->debug('filtered order item collection', $vendorInfo);
    }
}