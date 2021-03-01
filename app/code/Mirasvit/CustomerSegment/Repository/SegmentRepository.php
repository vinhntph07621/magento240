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



namespace Mirasvit\CustomerSegment\Repository;

use Mirasvit\CustomerSegment\Api\Data\Segment\StateInterface;
use Mirasvit\CustomerSegment\Api\Data\Segment\StateInterfaceFactory;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;
use Mirasvit\CustomerSegment\Api\Repository\SegmentRepositoryInterface;
use Mirasvit\CustomerSegment\Model\ResourceModel\Segment\CollectionFactory;
use Mirasvit\CustomerSegment\Model\SegmentFactory;

class SegmentRepository implements SegmentRepositoryInterface
{
    /**
     * @var StateInterface[]
     */
    private $stateRegistry = [];

    /**
     * @var SegmentFactory
     */
    private $factory;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var StateInterfaceFactory
     */
    private $stateFactory;

    /**
     * SegmentRepository constructor.
     * @param SegmentFactory $segmentFactory
     * @param CollectionFactory $collectionFactory
     * @param StateInterfaceFactory $stateFactory
     */
    public function __construct(
        SegmentFactory $segmentFactory,
        CollectionFactory $collectionFactory,
        StateInterfaceFactory $stateFactory
    ) {
        $this->factory           = $segmentFactory;
        $this->collectionFactory = $collectionFactory;
        $this->stateFactory      = $stateFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * {@inheritDoc}
     */
    public function get($id)
    {
        $segment = $this->create()->load($id);

        return $segment->getId() ? $segment : false;
    }

    /**
     * {@inheritDoc}
     */
    public function save(SegmentInterface $segment)
    {
        $dateTime = (new \DateTime())->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT);

        $segment->setUpdatedAt($dateTime);

        if (!$segment->getId()) {
            $segment->setCreatedAt($dateTime);
        }

        return $segment->save();
    }

    /**
     * {@inheritDoc}
     */
    public function delete(SegmentInterface $segment)
    {
        return $segment->delete();
    }

    /**
     * {@inheritDoc}
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @param SegmentInterface $segment
     * @return StateInterface
     */
    public function getState(SegmentInterface $segment)
    {
        $id = $segment->getId();

        if (!isset($this->stateRegistry[$id])) {
            $this->stateRegistry[$id] = $this->stateFactory->create();
        }

        return $this->stateRegistry[$id];
    }
}
