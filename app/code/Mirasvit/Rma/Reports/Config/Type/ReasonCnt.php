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
 * @package   mirasvit/module-rma
 * @version   2.1.18
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rma\Reports\Config\Type;

use Mirasvit\ReportApi\Api\Config\AggregatorInterface;
use Mirasvit\ReportApi\Api\Config\TypeInterface;
use Mirasvit\ReportApi\Config\Type\Number;

class ReasonCnt extends Number implements TypeInterface
{
    /**
     * @var \Mirasvit\Rma\Service\Report\Type\ReasonCnt
     */
    private $reasonCnt;

    /**
     * ReasonCnt constructor.
     * @param \Mirasvit\Rma\Service\Report\Type\ReasonCnt $reasonCnt
     */
    public function __construct(
        \Mirasvit\Rma\Service\Report\Type\ReasonCnt $reasonCnt
    ) {
        $this->reasonCnt = $reasonCnt;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'Mirasvit.reason_cnt';
    }

    /**
     * @return array|string[]
     */
    public function getAggregators()
    {
        return ['none', 'avg'];
    }

    /**
     * @return string
     */
    public function getJsType()
    {
        return self::JS_TYPE_NUMBER;
    }

    /**
     * @param number|string $actualValue
     * @param AggregatorInterface $aggregator
     * @return false|float|mixed|number|string
     */
    public function getFormattedValue($actualValue, AggregatorInterface $aggregator)
    {
        if ($actualValue === null) {
            return self::NA;
        }

        return $this->reasonCnt->get($actualValue);
    }
}
