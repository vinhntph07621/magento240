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



namespace Mirasvit\CustomerSegment\Factory\Segment;


use Magento\Framework\ObjectManagerInterface;
use Mirasvit\CustomerSegment\Api\Factory\Segment\ConditionFactoryInterface;
/**
 * Class allows to create conditions located at the namespace "\Mirasvit\CustomerSegment\Model\Segment\Condition"
 */
class ConditionFactory implements ConditionFactoryInterface
{
    /**
     * Namespace where located all the available conditions
     */
    const CONDITION_NAMESPACE = '\Mirasvit\CustomerSegment\Model\Segment\Condition\\';

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @inheritDoc
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @inheritDoc
     */
    public function create($requestedType, array $arguments = [])
    {
        $requestedType = self::CONDITION_NAMESPACE . ucfirst($requestedType);

        return $this->objectManager->create($requestedType, $arguments);
    }

    /**
     * @inheritDoc
     */
    public function setObjectManager(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }
}