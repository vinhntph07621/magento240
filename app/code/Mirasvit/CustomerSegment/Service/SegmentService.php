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

use Mirasvit\CustomerSegment\Api\Data\Segment\CustomerInterface as SegmentCustomerInterface;
use Mirasvit\CustomerSegment\Api\Repository\Segment\CustomerRepositoryInterface as SegmentCustomerRepositoryInterface;
use Mirasvit\CustomerSegment\Api\Service\SegmentServiceInterface;

class SegmentService implements SegmentServiceInterface
{
    /**
     * @var SegmentCustomerRepositoryInterface
     */
    private $segmentCustomerRepository;

    /**
     * SegmentService constructor.
     * @param SegmentCustomerRepositoryInterface $segmentCustomerRepository
     */
    public function __construct(
        SegmentCustomerRepositoryInterface $segmentCustomerRepository
    ) {
        $this->segmentCustomerRepository = $segmentCustomerRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomersCount($segmentId)
    {
        return $this->segmentCustomerRepository->getCollection()
            ->addFieldToFilter(SegmentCustomerInterface::SEGMENT_ID, $segmentId)
            ->getSize();
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomersInterceptionCount($segmentAId, $segmentBId)
    {
        $a = [];
        foreach ($this->getCustomers($segmentAId) as $item) {
            $a[] = $item->getCustomerId();
        }

        $b = [];
        foreach ($this->getCustomers($segmentBId) as $item) {
            $b[] = $item->getCustomerId();
        }

        return count(array_intersect($a, $b));
    }

    /**
     * {@inheritDoc}
     */
    public function getCustomers($segmentId)
    {
        return $this->segmentCustomerRepository->getCollection()
            ->addFieldToFilter('main_table.' . SegmentCustomerInterface::SEGMENT_ID, $segmentId);
    }
}
