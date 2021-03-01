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
use Mirasvit\CustomerSegment\Model\ResourceModel\Segment\Collection as SegmentCollection;

class Priority implements ArrayInterface
{
    /**
     * @var SegmentCollection
     */
    private $segmentCollection;

    /**
     * Priority constructor.
     *
     * @param SegmentCollection $segmentCollection
     */
    public function __construct(SegmentCollection $segmentCollection)
    {
        $this->segmentCollection = $segmentCollection;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        $options = [['value' => '', 'label' => __('-- Please Select --')]];
        $size = $this->segmentCollection->getSize();
        for ($i = 1; $i <= $size; $i++) {
            $options[] = ['value' => $i, 'label' => "{$i}"];
        }

        return $options;
    }
}