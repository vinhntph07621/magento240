<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-10
 * Time: 11:25
 */
namespace Omnyfy\VendorSubscription\Model\Source;

use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;
use Magento\Authorization\Model\UserContextInterface;

class VendorRoles extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    const XML_PATH_VENDOR_ROLES = 'omnyfy_subscription/vendor_type/allowed_roles';

    protected $resource;

    protected $config;

    protected $values;

    public function __construct(
        \Magento\Framework\App\ResourceConnection $resource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->resource = $resource;
        $this->config = $scopeConfig;
    }

    public function toValuesArray()
    {
        if (null == $this->values) {
            $result = [];
            $roles = $this->getAllRoles();
            foreach($roles as $role) {
                $result[$role['role_id']] = $role['role_name'];
            }
            $this->values = $result;
        }

        return $this->values;
    }

    public function getAllOptions()
    {
        $result = [];
        $values = $this->toValuesArray();
        foreach($values as $id => $name) {
            $result[] = [
                'value' => $id,
                'label' => $name
            ];
        }
        return $result;
    }

    private function getAllRoles()
    {
        $conn = $this->resource->getConnection();
        $table = $conn->getTableName('authorization_role');
        $select = $conn->select()
            ->from(
                $table,
                ['role_id', 'role_name']
            )
            ->where(
                'role_type=?',
                RoleGroup::ROLE_TYPE
            )
            ->where(
                'user_type=?',
                UserContextInterface::USER_TYPE_ADMIN
            )
            ->where(
                'role_name<>?',
                'Administrators'
            )
        ;

        return $conn->fetchAll($select);
    }
}