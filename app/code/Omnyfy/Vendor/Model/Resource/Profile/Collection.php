<?php
/**
 * Project: Multi Vendor M2.
 * User: jing
 * Date: 6/6/17
 * Time: 11:06 AM
 */

namespace Omnyfy\Vendor\Model\Resource\Profile;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Backend\Model\Session as BackendSession;
use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\State as AppState;
use Omnyfy\Vendor\Helper\User;
use Magento\Backend\Model\Auth\Session as AdminSession;


class Collection extends AbstractCollection
{
    protected $appState;

    protected $backendSession;

    protected $_userHelper;

    protected $_adminSession;

    protected $request;

    public function __construct(
        AppState $appState,
        BackendSession $backendSession,
        User $userHelper,
        AdminSession $adminSession,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null)
    {
        $this->appState = $appState;
        $this->backendSession = $backendSession;
        $this->_userHelper = $userHelper;
        $this->_adminSession = $adminSession;
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    protected function _construct()
    {
        $this->_init('Omnyfy\Vendor\Model\Profile', 'Omnyfy\Vendor\Model\Resource\Profile');
    }

    protected function filterAdminUser($userId)
    {
        $this->addFieldToFilter('profile_id',
            [
                'in' => $this->getConnection()->quoteInto(
                    'SELECT profile_id FROM ' . $this->getTable('omnyfy_vendor_profile_admin_user')
                    . ' WHERE admin_user_id=?',
                    $userId
                )
            ]
        );

        return $this;
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

        $action = $this->request->getActionName();

        if ($action == 'index') {
            if (!empty($vendorInfo)) {
                if ($vendorInfo['vendor_id']) {
                    $this->addFieldToFilter('vendor_id', $vendorInfo['vendor_id']);
                }
                if ($userStores) {
                    $this->filterWebsite($userStores);
                } else {
                    $this->filterWebsite($vendorInfo['website_ids']);
                }
                if (!isset($vendorInfo['is_vendor_admin']) || empty($vendorInfo['is_vendor_admin'])) {
                    //TODO: filter location by website and user
                    if ($userStores) {
                        $this->filterWebsite($userStores);
                    } else {
                        $this->filterWebsite($vendorInfo['website_ids']);
                    }
                }
            }
        }
        $this->_logger->debug('filtered vendor profile collection', $vendorInfo);
    }

    /**
     * Filter website
     *
     * @param int $websiteId
     */
    public function filterWebsite($websiteId)
    {
        if (!is_array($websiteId)) {
            $websiteId = [0, $websiteId];
        }

        $subSql = 'SELECT profile_id FROM ' . $this->getTable('omnyfy_vendor_profile')
            . ' WHERE website_id IN (?))'
        ;
        $this->addFieldToFilter('entity_id',
            [
                'in' => new \Zend_Db_Expr($this->getConnection()->quoteInto($subSql, $websiteId))
            ]
        );

        return $this;
    }
}