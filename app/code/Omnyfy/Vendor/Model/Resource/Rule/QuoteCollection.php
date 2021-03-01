<?php
/**
 * Project: Multi Vendor.
 * User: jing
 * Date: 3/11/18
 * Time: 11:01 AM
 */
namespace Omnyfy\Vendor\Model\Resource\Rule;

use Magento\Backend\App\Area\FrontNameResolver;

class QuoteCollection extends \Magento\SalesRule\Model\ResourceModel\Rule\Collection
{
    protected $appState;

    protected $backendSession;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Framework\App\State $appState,
        \Magento\Backend\Model\Session $backendSession,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        $this->appState = $appState;
        $this->backendSession = $backendSession;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $date, $connection, $resource);
    }

    /**
     * Add websites for load
     *
     * @return $this
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();
        return $this;
    }

    protected function _renderFiltersBefore() {
        parent::_renderFiltersBefore();

        if (FrontNameResolver::AREA_CODE != $this->appState->getAreaCode()) {
            return;
        }

        $vendorInfo = $this->backendSession->getVendorInfo();

        if (empty($vendorInfo)) {
            $this->_eventManager->dispatch('omnyfy_vendor_sales_rule_grid_render_filter_before', ['collection' => $this]);
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

        $this->_logger->debug('here2: '. get_class($this));

        $this->addFieldToFilter('vendor_id', intval($vendorInfo['vendor_id']));

        $this->_logger->debug('filtered sales rule collection', $vendorInfo);

        $this->_eventManager->dispatch('omnyfy_vendor_sales_rule_grid_render_filter_before', ['collection' => $this]);
    }

    public function getAllIds()
    {
        if (FrontNameResolver::AREA_CODE != $this->appState->getAreaCode()) {
            return parent::getAllIds();
        }

        $vendorInfo = $this->backendSession->getVendorInfo();
        if (empty($vendorInfo)) {
            return parent::getAllIds();
        }

        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');

        $idsSelect->where('vendor_id=?', intval($vendorInfo['vendor_id']));

        $this->_eventManager->dispatch('omnyfy_vendor_sales_rule_grid_get_all_ids_before', ['select' => $idsSelect, 'collection' => $this]);

        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }

}