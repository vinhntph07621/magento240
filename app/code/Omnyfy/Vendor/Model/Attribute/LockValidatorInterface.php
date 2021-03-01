<?php
/**
 * Project: Multi Vendor
 * User: jing
 * Date: 2019-04-30
 * Time: 16:51
 */
namespace Omnyfy\Vendor\Model\Attribute;

interface LockValidatorInterface
{
    /**
     * Check attribute lock state
     *
     * @param \Magento\Framework\Model\AbstractModel $object
     * @param null $attributeSet
     * @throws \Magento\Framework\Exception\LocalizedException
     *
     * @return void
     */
    public function validate(\Magento\Framework\Model\AbstractModel $object, $attributeSet = null);
}
 