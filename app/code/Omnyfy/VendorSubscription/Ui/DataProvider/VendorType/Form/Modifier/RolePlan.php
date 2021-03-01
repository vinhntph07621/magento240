<?php
/**
 * Project: Vendor Subscription
 * User: jing
 * Date: 2019-07-09
 * Time: 17:43
 */
namespace Omnyfy\VendorSubscription\Ui\DataProvider\VendorType\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;


class RolePlan implements ModifierInterface
{
    protected $locator;

    protected $helper;

    public function __construct(
        \Omnyfy\Vendor\Model\Vendor\Type\Locator\LocatorInterface $locator,
        \Omnyfy\VendorSubscription\Helper\Data $helper
    ) {
        $this->locator = $locator;
        $this->helper = $helper;
    }

    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    public function modifyData(array $data)
    {
        $vendorType = $this->locator->getVendorType();
        $typeId = $vendorType->getId();
        $rolePlans = $this->getRolePlanByVendorTypeId($typeId);
        if (!empty($rolePlans)) {
            if (!isset($data[$typeId]['role_plan'])) {
                $data[$typeId]['role_plan'] = $rolePlans;
            }
            if (!isset($data[$typeId]['vendor_type']['role_plan'])) {
                $data[$typeId]['vendor_type']['role_plan'] = $rolePlans;
            }
        }

        return $data;
    }

    protected function getRolePlanByVendorTypeId($typeId)
    {
        return $this->helper->getRolePlanByVendorTypeId($typeId);
    }
}
 