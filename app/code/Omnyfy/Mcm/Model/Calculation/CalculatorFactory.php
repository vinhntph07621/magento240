<?php

namespace Omnyfy\Mcm\Model\Calculation;

use Magento\Framework\Exception\ConfigurationMismatchException;
use Magento\Framework\ObjectManagerInterface;
use Omnyfy\Mcm\Helper\Data as FeeHelper;

class CalculatorFactory
{
    /**
     * @var FeeHelper
     */
    protected $helper;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * CalculationFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param FeeHelper $helper
     */
    public function __construct(ObjectManagerInterface $objectManager, FeeHelper $helper)
    {
        $this->helper = $helper;
        $this->objectManager = $objectManager;
    }

    /**
     * @return Calculator\CalculatorInterface
     * @throws ConfigurationMismatchException
     */
    public function get()
    {
        return $this->objectManager->get(Calculator\FixedCalculator::class);
    }

}