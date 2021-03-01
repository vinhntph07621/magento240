<?php
/**
 * Project: apcd.
 * User: jing
 * Date: 30/8/18
 * Time: 11:39 AM
 */
namespace Omnyfy\Vendor\Plugin\Rule\Condition;

class AbstractCondition
{
    public function afterGetOperatorSelectOptions(
        \Magento\Rule\Model\Condition\AbstractCondition $subject,
        $result
    )
    {
        $attribute = $subject->getAttribute();
        if ('location_id' == $attribute || 'vendor_id' == $attribute) {
            $operators = [
                '==' => 'is',
                '!=' => 'is not',
                '()' => 'is one of',
                '!()' => 'is not one of'
            ];
            $type = $subject->getInputType();
            $result = [];
            $operatorByType = $subject->getOperatorByInputType();
            foreach ($operators as $operatorKey => $operator) {
                if (!$operatorByType || in_array($operatorKey, $operatorByType[$type])) {
                    $result[] = ['value' => $operatorKey, 'label' => $operator];
                }
            }
        }
        return $result;
    }
}