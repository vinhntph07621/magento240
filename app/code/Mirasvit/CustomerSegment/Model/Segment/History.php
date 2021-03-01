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


use Magento\Framework\Model\AbstractModel;
use Mirasvit\CustomerSegment\Api\Data\Segment\HistoryInterface;

class History extends AbstractModel implements HistoryInterface
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(\Mirasvit\CustomerSegment\Model\ResourceModel\Segment\History::class);
    }

    /**
     * @inheritDoc
     */
    public function getSegmentId()
    {
        return $this->getData(self::SEGMENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setSegmentId($segmentId)
    {
        $this->setData(self::SEGMENT_ID, $segmentId);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAction()
    {
        return $this->getData(self::ACTION);
    }

    /**
     * @inheritDoc
     */
    public function setAction($action)
    {
        $this->setData(self::ACTION, $action);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * @inheritDoc
     */
    public function setMessage($message)
    {
        $this->setData(self::MESSAGE, $message);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritDoc
     */
    public function setType($type)
    {
        $this->setData(self::TYPE, $type);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getAffectedRows()
    {
        return $this->getData(self::AFFECTED_ROWS);
    }

    /**
     * @inheritDoc
     */
    public function setAffectedRows($count)
    {
        $this->setData(self::AFFECTED_ROWS, $count);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(self::CREATED_AT, $createdAt);

        return $this;
    }
}