<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-customer-segment
 * @version   1.0.51
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\CustomerSegment\Service;


use Mirasvit\CustomerSegment\Api\Service\OperatorConversionInterface;

class OperatorConversion implements OperatorConversionInterface
{
    /**
     * {@inheritDoc}
     */
    public function getSqlOperator($operator)
    {
        /*
            '{}'  __('contains'),
            '!{}' __('does not contain'),
            '()'  __('is one of'),
            '!()' __('is not one of'),
            requires custom selects
        */

        switch ($operator) {
            case '==':
                return '=';
            case '!=':
                return '<>';
            case '{}':
                return 'LIKE';
            case '!{}':
                return 'NOT LIKE';
            case '()':
                return 'IN';
            case '!()':
                return 'NOT IN';
            case '[]':
                return 'FIND_IN_SET(%s, %s)';
            case '![]':
                return 'FIND_IN_SET(%s, %s) IS NULL';
            case 'between':
                return 'BETWEEN %s AND %s';
            case '>':
            case '<':
            case '>=':
            case '<=':
                return $operator;
            default:
                throw new \Exception(__('Unknown operator specified.'));
        }
    }
}