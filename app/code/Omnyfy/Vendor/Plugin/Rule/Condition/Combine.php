<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 6/2/20
 * Time: 3:40 pm
 */
namespace Omnyfy\Vendor\Plugin\Rule\Condition;

class Combine
{
    public function aroundValidate($subject, callable $process, \Magento\Framework\Model\AbstractModel $model)
    {
        if ($model instanceof \Magento\Quote\Model\Quote\Address) {
            return $process($model);
        }

        $vendorId = $subject->getRule()->getVendorId();
        if (!empty($vendorId) && $model->getVendorId() != $vendorId) {
            return false;
        }

        return $process($model);
    }
}
 