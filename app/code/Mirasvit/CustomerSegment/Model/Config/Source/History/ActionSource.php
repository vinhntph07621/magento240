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



namespace Mirasvit\CustomerSegment\Model\Config\Source\History;

use Magento\Framework\Option\ArrayInterface;
use Mirasvit\CustomerSegment\Api\Data\Segment\HistoryInterface;

class ActionSource implements ArrayInterface
{
    /**
     * Convert data from tree format to flat format
     * @return array
     */
    public function toFlatArray()
    {
        $options = [];
        foreach ($this->toOptionArray() as $item) {
            if (isset($item['value']) && isset($item['label'])) {
                $options[$item['value']] = $item['label'];
            }
        }

        return $options;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => HistoryInterface::ACTION_START,
                'label' => __('Segment Refresh Started'),
            ],
            [
                'value' => HistoryInterface::ACTION_FINISH,
                'label' => __('Segment Refresh Finished'),
            ],
            [
                'value' => HistoryInterface::ACTION_START_ITERATION,
                'label' => __('Segment Refresh Iteration Started'),
            ],
            [
                'value' => HistoryInterface::ACTION_FINISH_ITERATION,
                'label' => __('Segment Refresh Iteration Finished'),
            ],
            [
                'value' => HistoryInterface::ACTION_ADD,
                'label' => __('Add Customer'),
            ],
            [
                'value' => HistoryInterface::ACTION_REMOVE,
                'label' => __('Remove Customer'),
            ],
            [
                'value' => HistoryInterface::ACTION_GROUP,
                'label' => __('Customer Group Change'),
            ],
        ];
    }
}