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


interface HistoryInterface
{
    const ID            = 'history_id';
    const SEGMENT_ID    = 'segment_id';
    const ACTION        = 'action';
    const TYPE          = 'type';
    const AFFECTED_ROWS = 'affected_rows';
    const MESSAGE       = 'message';
    const CREATED_AT    = 'created_at';

    /**
     * Possible history actions
     */
    const ACTION_START            = 'start';
    const ACTION_FINISH           = 'finished';
    const ACTION_START_ITERATION  = 'start_iteration';
    const ACTION_FINISH_ITERATION = 'finished_iteration';
    const ACTION_ADD              = 'add';
    const ACTION_REMOVE           = 'remove';
    const ACTION_GROUP            = 'group';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return int
     */
    public function getSegmentId();

    /**
     * @param int $segmentId
     *
     * @return $this
     */
    public function setSegmentId($segmentId);

    /**
     * @return string
     */
    public function getAction();

    /**
     * @param string $action
     *
     * @return $this
     */
    public function setAction($action);

    /**
     * @return string
     */
    public function getMessage();

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message);

    /**
     * @return int
     */
    public function getType();

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setType($type);

    /**
     * @return int
     */
    public function getAffectedRows();

    /**
     * @param int $count
     *
     * @return $this
     */
    public function setAffectedRows($count);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     *
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return array
     */
    public function getData();

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
     *
     * @return $this
     */
    public function setData($key, $value = null);

    /**
     * Delete segment customer from database.
     *
     * @return $this
     * @throws \Exception
     * @deprecated
     */
    public function delete();

    /**
     * Save segment customer data.
     *
     * @return $this
     * @throws \Exception
     * @deprecated
     */
    public function save();
}