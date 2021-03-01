<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-24
 * Time: 16:17
 */
namespace Omnyfy\Vendor\Plugin\Amasty\AdminActionLog;

class Helper
{
    public function aroundNeedOldData($subject, callable $process, $object)
    {
        if ($object instanceof \Magento\Eav\Model\Entity\Attribute\Group) {
            return false;
        }

        return $process($object);
    }
}
