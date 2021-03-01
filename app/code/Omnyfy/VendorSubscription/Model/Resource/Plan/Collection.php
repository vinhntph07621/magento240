<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-01
 * Time: 17:42
 */
namespace Omnyfy\VendorSubscription\Model\Resource\Plan;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected $_idFieldName = 'plan_id';

    protected function _construct()
    {
        $this->_init('Omnyfy\VendorSubscription\Model\Plan', 'Omnyfy\VendorSubscription\Model\Resource\Plan');
    }

    public function addRoleIdJoin($vendorTypeId)
    {
        if ($this->getFlag('has_role_id_join')) {
            return $this;
        }

        $table = $this->getTable('omnyfy_vendorsubscription_vendor_type_plan');
        $this->getSelect()->join(
            ['vtp' => $table],
            'main_table.plan_id=vtp.plan_id AND vtp.type_id='. $vendorTypeId,
            ['role_id' => 'vtp.role_id']
        );

        $this->setFlag('has_role_id_join');
        return $this;
    }
}
