<?php
/**
 * Project: Mcm
 * User: jing
 * Date: 2019-03-26
 * Time: 11:46
 */

namespace Omnyfy\Mcm\Model\ResourceModel;

use Magento\Framework\App\State as AppState;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Backend\App\Area\FrontNameResolver;

class AbstractCollection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_appState;

    protected $_backendSession;

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        AppState $appState,
        BackendSession $backendSession,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);

        $this->_appState = $appState;
        $this->_backendSession = $backendSession;
    }

    public function getAllIds()
    {
        $idsSelect = clone $this->getSelect();
        $idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
        $idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
        $idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);

        $idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');

        if (FrontNameResolver::AREA_CODE == $this->_appState->getAreaCode()) {
            $vendorId = $this->_backendSession->getCurrentVendorId();

            if (!empty($vendorId)) {
                $this->getSelect()->where('vendor_id=' . $vendorId);
            }
        }

        return $this->getConnection()->fetchCol($idsSelect, $this->_bindParams);
    }
}