<?php
/**
 * Project: Core
 * User: jing
 * Date: 20/9/19
 * Time: 10:57 am
 */
namespace Omnyfy\Core\Model\Source;

abstract class AbstractSource extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    abstract function toValuesArray();

    public function toOptionArray()
    {
        $result = [];
        foreach($this->toValuesArray() as $key => $val) {
            $result[] = [
                'value' => $key,
                'label' => $val
            ];
        }
        return $result;
    }

    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}