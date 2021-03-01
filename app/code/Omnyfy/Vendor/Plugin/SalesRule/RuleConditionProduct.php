<?php
/**
 * Project: apcd.
 * User: jing
 * Date: 23/10/18
 * Time: 12:27 PM
 */
namespace Omnyfy\Vendor\Plugin\SalesRule;

class RuleConditionProduct
{
    public function beforeValidate($subject, \Magento\Framework\Model\AbstractModel $model)
    {
        if (($model->getItemId() || $model->getAddressItemId())
            && ('location_id' == $subject->getAttribute() || 'vendor_id'== $subject->getAttribute())) {
            if ($model->getLocationId()) {
                $model->getProduct()->setData('location_id', $model->getLocationId());
            }

            if ($model->getVendorId()) {
                $model->getProduct()->setData('vendor_id', $model->getVendorId());
            }
        }

        return [$model];
    }

    public function aroundValidate($subject, callable $process, \Magento\Framework\Model\AbstractModel $model)
    {
        $vendorId = $subject->getRule()->getVendorId();
        if (!empty($vendorId) && $model->getVendorId() != $vendorId) {
            return false;
        }

        return $process($model);
    }
}