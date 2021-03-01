<?php
namespace Omnyfy\Vendor\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\UserContextInterface;

class User extends AbstractHelper
{
    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_adminSession;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Recipient email config path
     */
    const OMNYFY_USER_ROLE_EDIT = 'omnyfy_vendor_permissions/vendor/omnyfy_user_role_edit';
    const OMNYFY_NON_RESTRCT_USER = 'omnyfy_vendor_permissions/vendor/omnyfy_no_user_role_restrictions';

    public function __construct(
        Context $context,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_resourceConnection = $resourceConnection;
        $this->_adminSession = $adminSession;
        $this->_scopeConfig = $scopeConfig;

        parent::__construct($context);
    }

    public function getUserVendor($userId)
    {
        if (!empty($userId)) {
            $connection = $this->_resourceConnection->getConnection();
            $tableName = $connection->getTableName($this->getAdminUserTable());
            $sql = "Select `vendor_id` FROM " . $tableName . " WHERE `user_id` = " . $userId;
            $vendorId = $connection->fetchOne($sql);

            if (!empty($vendorId)) {
                return $vendorId;
            } else {
                return false;
            }
        }
    }

    public function getUserStores($userId)
    {
        if (!empty($userId)) {
            $connection = $this->_resourceConnection->getConnection();
            $tableName = $connection->getTableName($this->getUserStoresTable());
            $sql = "Select `store_id` FROM " . $tableName . " WHERE `user_id` = " . $userId;
            $userStores = $connection->fetchOne($sql);

            if (!empty($userStores) && count($userStores) > 0) {
                $userStoresUnserialized = unserialize(($userStores));
                return $userStoresUnserialized;
            } else {
                return false;
            }
        }
    }

    public function getAdminUserTable()
    {
        return 'omnyfy_vendor_vendor_admin_user';
    }

    public function getUserStoresTable()
    {
        return 'omnyfy_vendor_vendor_user_stores';
    }

    public function getUserEditRoles()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;

        return explode(',', $this->_scopeConfig->getValue(self::OMNYFY_USER_ROLE_EDIT, $storeScope));
    }

    public function getNonRestrictUser(){
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        return explode(',', $this->_scopeConfig->getValue(self::OMNYFY_NON_RESTRCT_USER, $storeScope));
    }


}
