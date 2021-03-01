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
 * @package   mirasvit/module-event
 * @version   1.2.36
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Event\EventData;

use Magento\Review\Model\Review;
use Mirasvit\Event\Api\Data\EventDataInterface;
use Mirasvit\Event\EventData\Condition\ReviewCondition;

class ReviewData extends Review implements EventDataInterface
{
    use ContextTrait;

    const ID = 'review_id';

    const IDENTIFIER = 'review';

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return self::IDENTIFIER;
    }

    /**
     * @return string
     */
    public function getConditionClass()
    {
        return ReviewCondition::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return __('Review');
    }

    /**
     * {@inheritdoc}
     */
    public function getAttributes()
    {
        return [];
    }
}
