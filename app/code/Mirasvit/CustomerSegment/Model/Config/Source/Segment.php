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


use Mirasvit\CustomerSegment\Api\Data\Segment\OptionSourceInterface;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface;

class Segment implements OptionSourceInterface
{
    /**
     * @var SegmentRepositoryInterface
     */
    private $segmentRepository;

    /**
     * @param SegmentRepositoryInterface $segmentRepository
     */
    public function __construct(
        SegmentRepositoryInterface $segmentRepository
    ) {
        $this->segmentRepository = $segmentRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray($all = false)
    {
        $options = [];
        if ($all) {
            $options[] = [
                'label' => __('All Segments'),
                'value' => 0,
            ];
        }

        return array_merge($options, $this->segmentRepository->getCollection()
            ->load()
            ->toOptionArray());
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionHash($all = false)
    {
        $hash = $this->segmentRepository->getCollection()
            ->load()
            ->toOptionHash();

        if ($all) {
            $hash[0] = __('All Segments');
        }

        return $hash;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }
}
