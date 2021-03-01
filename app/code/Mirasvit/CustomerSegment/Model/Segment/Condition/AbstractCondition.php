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



namespace Mirasvit\CustomerSegment\Model\Segment\Condition;

use Magento\Rule\Model\Condition\Context;

abstract class AbstractCondition extends \Magento\Rule\Model\Condition\AbstractCondition
{
    /**
     * AbstractCondition constructor.
     * @param Context $context
     * @param array $data
     * @throws \Exception
     */
    public function __construct(Context $context, array $data = [])
    {
        $data['type'] = get_class($this);

        if (!isset($data['label'])) {
            throw new \Exception('label is empty');
        }

        parent::__construct($context, $data);
    }

    /**
     * @return mixed
     */
    abstract public function getAttributeOption();
}
