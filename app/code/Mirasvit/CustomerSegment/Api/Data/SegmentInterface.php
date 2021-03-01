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



namespace Mirasvit\CustomerSegment\Api\Data;

interface SegmentInterface
{
    const TABLE_NAME = 'mst_customersegment_segment';

    const ID                    = 'segment_id';
    const TITLE                 = 'title';
    const DESCRIPTION           = 'description';
    const TYPE                  = 'type';
    const WEBSITE_ID            = 'website_id';
    const CONDITIONS_SERIALIZED = 'conditions_serialized';
    const PRIORITY              = 'priority';
    const TO_GROUP_ID           = 'to_group_id';
    const STATUS                = 'status';
    const IS_MANUAL             = 'is_manual';
    const CREATED_AT            = 'created_at';
    const UPDATED_AT            = 'updated_at';

    const STATUS_ACTIVE   = 1;
    const STATUS_INACTIVE = 0;

    const TYPE_CUSTOMER = 1;
    const TYPE_GUEST    = 2;
    const TYPE_ALL      = 3;

    const QUEUE_CSS_REFRESHED = 'mirasvit.css.refreshed';

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function getTitle();

    /**
     * @param string $title
     *
     * @return $this
     */
    public function setTitle($title);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description);

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
    public function getWebsiteId();

    /**
     * @param int $websiteId
     *
     * @return $this
     */
    public function setWebsiteId($websiteId);

    /**
     * @return int
     */
    public function getToGroupId();

    /**
     * @param int $groupId
     *
     * @return $this
     */
    public function setToGroupId($groupId);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @param int $status
     *
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return int
     */
    public function getPriority();

    /**
     * @param int $priority
     *
     * @return $this
     */
    public function setPriority($priority);

    /**
     * @return bool
     */
    public function getIsManual();

    /**
     * @param bool $isManual
     *
     * @return $this
     */
    public function setIsManual($isManual);

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
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * @param string $value
     * @return $this
     */
    public function setConditionsSerialized($value);

    /**
     * @return \Mirasvit\CustomerSegment\Model\Segment\Rule
     */
    public function getRule();
}