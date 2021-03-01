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



namespace Mirasvit\CustomerSegment\Model\Config\Source;


use Magento\Framework\Option\ArrayInterface;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;

class Type implements ArrayInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => SegmentInterface::TYPE_ALL, 'label' => __('Registered and Guest Customers')],
            ['value' => SegmentInterface::TYPE_CUSTOMER, 'label' => __('Registered Customers')],
            ['value' => SegmentInterface::TYPE_GUEST, 'label' => __('Guest Customers')]
        ];
    }
}