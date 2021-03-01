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



namespace Mirasvit\CustomerSegment\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Mirasvit\CustomerSegment\Api\Data\SegmentInterface;

class Segment extends AbstractModel implements SegmentInterface
{
    /**
     * @var Segment\Rule
     */
    private $rule;

    /**
     * @var Segment\RuleFactory
     */
    private $ruleFactory;

    /**
     * Segment constructor.
     * @param Segment\RuleFactory $ruleFactory
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Segment\RuleFactory $ruleFactory,
        Context $context,
        Registry $registry
    ) {
        $this->ruleFactory = $ruleFactory;

        parent::__construct($context, $registry);
    }


    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Segment::class);
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function getTitle()
    {
        return $this->getData(self::TITLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setTitle($title)
    {
        $this->setData(self::TITLE, $title);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->setData(self::DESCRIPTION, $description);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * {@inheritdoc}
     */
    public function setType($type)
    {
        $this->setData(self::TYPE, $type);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setWebsiteId($websiteId)
    {
        $this->setData(self::WEBSITE_ID, $websiteId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPriority()
    {
        return $this->getData(self::PRIORITY);
    }

    /**
     * @inheritdoc
     */
    public function setPriority($priority)
    {
        $this->setData(self::PRIORITY, $priority);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsManual()
    {
        return $this->getData(self::IS_MANUAL);
    }

    /**
     * @inheritdoc
     */
    public function setIsManual($isManual)
    {
        $this->setData(self::IS_MANUAL, $isManual);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getToGroupId()
    {
        return $this->getData(self::TO_GROUP_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setToGroupId($groupId)
    {
        $this->setData(self::TO_GROUP_ID, $groupId);

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
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(self::CREATED_AT, $createdAt);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(self::UPDATED_AT, $updatedAt);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setConditionsSerialized($value)
    {
        return $this->setData(self::CONDITIONS_SERIALIZED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRule()
    {
        if (!$this->rule) {
            $this->rule = $this->ruleFactory->create()
                ->setData(self::CONDITIONS_SERIALIZED, $this->getData(self::CONDITIONS_SERIALIZED));
        }

        return $this->rule;
    }
}
