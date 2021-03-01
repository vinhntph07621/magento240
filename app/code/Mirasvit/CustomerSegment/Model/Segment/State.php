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



namespace Mirasvit\CustomerSegment\Model\Segment;

use Magento\Framework\DataObject;
use Mirasvit\CustomerSegment\Api\Data\Segment\StateInterface as SegmentStateInterface;
use Mirasvit\CustomerSegment\Api\Repository\Candidate\FinderRepositoryInterface;
use Mirasvit\CustomerSegment\Api\Service\Candidate\FinderInterface;

class State extends DataObject implements SegmentStateInterface
{
    /**
     * @var FinderRepositoryInterface
     */
    private $finderRepository;

    /**
     * @var FinderInterface[]
     */
    private $steps = [];

    /**
     * State constructor.
     * @param FinderRepositoryInterface $finderRepository
     * @param array $data
     */
    public function __construct(
        FinderRepositoryInterface $finderRepository,
        array $data = []
    ) {
        $this->finderRepository = $finderRepository;

        parent::__construct($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getSize()
    {
        return $this->getData(self::SIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function setSize($size)
    {
        $this->setData(self::SIZE, $size);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIndex()
    {
        return (int)$this->getData(self::INDEX);
    }

    /**
     * {@inheritdoc}
     */
    public function setIndex($index)
    {
        $this->setData(self::INDEX, $index);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLimit()
    {
        return $this->getData(self::LIMIT);
    }

    /**
     * {@inheritdoc}
     */
    public function setLimit($limit)
    {
        $this->setData(self::LIMIT, $limit);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->setData(self::STATUS, $status);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStep()
    {
        return $this->getData(self::STEP);
    }

    /**
     * {@inheritdoc}
     */
    public function setStep($step)
    {
        $this->setData(self::STEP, $step);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSteps()
    {
        return $this->steps;
    }

    /**
     * {@inheritdoc}
     */
    public function setSteps(array $finders = [])
    {
        $this->steps = $finders;

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function getStepStatus()
    {
        return $this->getData(self::STEP_STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStepStatus($status)
    {
        $this->setData(self::STEP_STATUS, $status);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalSize()
    {
        return $this->getData(self::TOTAL_SIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalSize($size)
    {
        $this->setData(self::TOTAL_SIZE, $size);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerTotalSize()
    {
        return $this->getData(self::CUSTOMER_TOTAL_SIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCustomerTotalSize($size)
    {
        $this->setData(self::CUSTOMER_TOTAL_SIZE, $size);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getGuestTotalSize()
    {
        return $this->getData(self::GUEST_TOTAL_SIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function setGuestTotalSize($size)
    {
        $this->setData(self::GUEST_TOTAL_SIZE, $size);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStepTotalSize()
    {
        return $this->getData($this->getStep() . '_' . self::TOTAL_SIZE);
    }

    /**
     * {@inheritdoc}
     */
    public function getProgress()
    {
        $progress = [];

        if ($this->getStatus() === self::STATUS_COMPLETED) {
            $progress = [
                self::STATUS_COMPLETED => [
                    'count' => $this->getTotalSize(),
                ],
            ];
        } else {
            // set steps
            foreach ($this->getSteps() as $finder) {
                $progress['steps'][] = [
                    'code'   => $finder->getCode(),
                    'name'   => $finder->getName(),
                    'status' => $this->getData($finder->getCode()),
                ];
            }

            // set current step progress details
            if ($this->getStep()) {
                $finder    = $this->finderRepository->getByCode($this->getStep());
                $totalSize = $this->getData($finder->getCode() . '_total_size');
                // percent of processed items within all iterations
                $percent             = $totalSize ? round(100 / $totalSize * $this->getSize()) : 0;
                $progress['current'] = [
                    'name'     => $finder->getName(),
                    'eta'      => $this->getEta($this->getIndex(), $totalSize, $percent, $this->getStartedAt()) ? : '',
                    'percent'  => $percent . '%',
                    'position' => __('%1 out of %2', $this->getSize(), $totalSize),
                ];
            }
        }

        return $progress;
    }

    /**
     * Calculate ETA based on length, current position and time.
     *
     * @param int $index - number of processed items in current iteration
     * @param int $totalSize - total number of processed items in all iterations
     * @param int $percent - percent of processed items
     * @param int $startTime - start time of current iteration
     *
     * @return bool|string
     */
    public function getEta($index, $totalSize, $percent, $startTime)
    {
        if (!$totalSize || !$index) {
            return false;
        }

        $left        = 100 - $percent;
        $iterPercent = 100 / $totalSize * $index;

        if ($left) {
            $eta = (microtime(true) - $startTime) * ($left / $iterPercent);
            if ($eta > 3600) {
                $etaMsg = date('h:i:s', $eta);
            } else {
                $etaMsg = date('i:s', $eta);
            }

            return __('ETA %1', $etaMsg)->__toString();
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function processStep($stepCode)
    {
        $this->setData($stepCode, self::STEP_STATUS_PROCESSING);
        $this->setStepStatus(self::STEP_STATUS_PROCESSING);
        $this->setStep($stepCode);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function finishStep($stepCode)
    {
        $this->setData($stepCode, self::STEP_STATUS_FINISHED);
        $this->setStepStatus(self::STEP_STATUS_FINISHED);
        $this->setSize(0);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isFinished()
    {
        foreach ($this->getSteps() as $step) {
            if ($this->getData($step->getCode()) != self::STEP_STATUS_FINISHED) {
                return false;
            }
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function setStartedAt($startedAt)
    {
        $this->setData(self::STARTED_AT, $startedAt);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStartedAt()
    {
        return $this->getData(self::STARTED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setRuleStartTime($time)
    {
        $this->setData(self::RULE_START_TIME, $time);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRuleStartTime()
    {
        return $this->getData(self::RULE_START_TIME);
    }

    /**
     * {@inheritdoc}
     */
    public function getStepPercent()
    {
        if ($this->getStepTotalSize()) {
            return ceil(100 / $this->getStepTotalSize() * $this->getIndex());
        }

        return 0;
    }
}
