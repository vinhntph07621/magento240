<?php
namespace Omnyfy\Vendor\Model\Config\Source;

use Magento\Authorization\Model\Acl\Role\Group as RoleGroup;

class UserRolesValues implements \Magento\Framework\Option\ArrayInterface
{
    protected $roleCollectionFactory;

    protected $_options;

    public function __construct(
        \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $roleCollectionFactory
    )
    {
        $this->roleCollectionFactory = $roleCollectionFactory;
    }

    public function toOptionArray()
    {
        if (null == $this->_options) {
            $result = [];
            $collection = $this->roleCollectionFactory->create();

            $collection->addFieldToFilter('role_type', RoleGroup::ROLE_TYPE);

            foreach($collection as $roleType)
            {
                $result[] = [
                    "value" => $roleType->getRoleName(),
                    "label" => $roleType->getRoleName()
                ];
            }
            $this->_options = $result;
        }

        return $this->_options;
    }

    public function toValuesArray()
    {
        $options = $this->toOptionArray();
        $result = [];
        foreach($options as $option) {
            $result[$option['value']] = $option['label'];
        }
        return $result;
    }
}
