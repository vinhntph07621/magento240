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



namespace Mirasvit\CustomerSegment\Api\Data\Segment;


use Mirasvit\CustomerSegment\Api\Service\Candidate\FinderInterface;

interface StateInterface
{
    /** FIELDS */
    const SIZE                = 'size';
    const INDEX               = 'idx';
    const LIMIT               = 'limit';
    const STATUS              = 'status';
    const STEP                = 'step';
    const STEPS               = 'steps';
    const STEP_STATUS         = 'step_status';
    const TOTAL_SIZE          = 'total_size';
    const GUEST_TOTAL_SIZE    = 'guest_total_size';
    const CUSTOMER_TOTAL_SIZE = 'customer_total_size';
    const PROGRESS            = 'progress';
    const STARTED_AT          = 'started_at';
    const RULE_START_TIME     = 'rule_start_time';

    /** STATUSES */
    const STATUS_NEW       = 'new';
    const STATUS_CONTINUE  = 'continue';
    const STATUS_COMPLETED = 'completed';
    const STATUS_ERROR     = 'error';

    const STEP_STATUS_PROCESSING = 'processing';
    const STEP_STATUS_FINISHED   = 'finished';

    /**
     * @return int
     */
    public function getSize();

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setSize($size);

    /**
     * @return int
     */
    public function getIndex();

    /**
     * @param int $index
     *
     * @return $this
     */
    public function setIndex($index);

    /**
     * @return int
     */
    public function getLimit();

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function setLimit($limit);

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getStep();

    /**
     * @param string $step
     *
     * @return $this
     */
    public function setStep($step);

    /**
     * @return FinderInterface[]
     */
    public function getSteps();

    /**
     * @param FinderInterface[] $finders
     *
     * @return $this
     */
    public function setSteps(array $finders = []);

    /**
     * @return string
     */
    public function getStepStatus();

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStepStatus($status);

    /**
     * @return int
     */
    public function getTotalSize();

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setTotalSize($size);

    /**
     * @return int
     */
    public function getCustomerTotalSize();

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setCustomerTotalSize($size);

    /**
     * @return int
     */
    public function getGuestTotalSize();

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setGuestTotalSize($size);

    /**
     * Get current step's total size of candidates.
     *
     * @return int
     */
    public function getStepTotalSize();

    /**
     * @return array
     */
    public function getProgress();

    /**
     * Start step processing.
     *
     * @param string $stepCode
     *
     * @return $this
     */
    public function processStep($stepCode);

    /**
     * Finish step processing.
     *
     * @param string $stepCode
     *
     * @return $this
     */
    public function finishStep($stepCode);

    /**
     * Check whether refresh process has been finished or no.
     *
     * @return bool
     */
    public function isFinished();

    /**
     * @param float|int $startedAt
     *
     * @return $this
     */
    public function setStartedAt($startedAt);

    /**
     * @return float|int
     */
    public function getStartedAt();

    /**
     * @param float|int $time
     *
     * @return $this
     */
    public function setRuleStartTime($time);

    /**
     * @return float|int
     */
    public function getRuleStartTime();

    /**
     * Get percent of candidates processed this step.
     *
     * @return int
     */
    public function getStepPercent();

    /**
     * Object data getter
     *
     * If $key is not defined will return all the data as an array.
     * Otherwise it will return value of the element specified by $key.
     * It is possible to use keys like a/b/c for access nested array data
     *
     * If $index is specified it will assume that attribute data is an array
     * and retrieve corresponding member. If data is the string - it will be explode
     * by new line character and converted to array.
     *
     * @param string     $key
     * @param string|int $index
     * @return mixed
     */

    public function getData($key = '', $index = null);

    /**
     * Overwrite data in the object.
     *
     * The $key parameter can be string or array.
     * If $key is string, the attribute value will be overwritten by $value
     *
     * If $key is an array, it will overwrite all the data in the object.
     *
     * @param string|array  $key
     * @param mixed         $value
     * @return $this
     */
    public function setData($key, $value = null);

    /**
     * Add data to the object.
     *
     * Retains previous data in the object.
     *
     * @param array $arr
     * @return $this
     */
    public function addData(array $arr);
}