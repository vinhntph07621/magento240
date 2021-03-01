<?php

namespace Omnyfy\Mcm\Model\Calculation\Calculator;

use Omnyfy\Mcm\Helper\Data as FeeHelper;

abstract class AbstractCalculator implements CalculatorInterface
{
    /**
     * @var FeeHelper
     */
    protected $_helper;

    /**
     * AbstractCalculation constructor.
     *
     * @param FeeHelper $helper
     */
    public function __construct(FeeHelper $helper)
    {
        $this->_helper = $helper;
    }
}